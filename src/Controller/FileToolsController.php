<?php

namespace Olz\Controller;

use Olz\Utils\FileUtils;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class FileToolsController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/file_tools/thumb/{db_table}${id}${index}${dimension}.svg', requirements: [
        'db_table' => '[a-z_]+',
        'id' => '\d+',
        'dimension' => '\d+',
    ])]
    public function thumb(
        Request $request,
        LoggerInterface $logger,
        string $db_table,
        int $id,
        string $index,
        int $dimension,
    ): Response {
        $data_path = $this->envUtils()->getDataPath();

        session_write_close();
        if (!isset(FileUtils::TABLES_FILE_DIRS[$db_table])) {
            throw new NotFoundHttpException("No such DB table: {$db_table}");
        }

        $db_filepath = FileUtils::TABLES_FILE_DIRS[$db_table];
        if ($id <= 0) {
            throw new NotFoundHttpException("Invalid ID: {$id}");
        }
        $is_migrated = !(is_numeric($index) && intval($index) > 0 && intval($index) == $index);
        if ($is_migrated) {
            if (!preg_match("/^[0-9A-Za-z_\\-]{24}\\.\\S{1,10}$/", $index)) {
                throw new NotFoundHttpException("Invalid index (=hash; in thumb): {$index}");
            }
        } else {
            if ($index <= 0) {
                throw new NotFoundHttpException("Invalid index (in thumb): {$index}");
            }
        }
        if ($is_migrated) {
            $this->log()->notice("Remaining migrated file icon fetching: {$db_table}\${$id}\${$index}\${$dimension}.svg");
            preg_match("/^[0-9A-Za-z_\\-]{24}\\.(\\S{1,10})$/", $index, $matches);
            $extension_icon = FileUtils::EXTENSION_ICONS[$matches[1] ?? ''] ?? '';
            $thumbfile = __DIR__."/../../assets/icns/link_{$extension_icon}_16.svg";
            if (!is_file($thumbfile)) {
                $thumbfile = __DIR__."/../assets/icns/link_any_16.svg";
            }
            $response = new Response(file_get_contents($thumbfile) ?: null);
            $response->headers->set('Cache-Control', 'max-age=86400');
            $response->headers->set('Content-Type', 'image/svg+xml');
            return $response;
        }
        $files = scandir($data_path.$db_filepath."/".$id) ?: [];
        for ($i = 0; $i < count($files); $i++) {
            if (preg_match("/^([0-9]{3})\\.([a-zA-Z0-9]+)$/", $files[$i], $matches)) {
                if (intval($matches[1]) == $index) {
                    $thumbfile = __DIR__."/../../assets/icns/link_".FileUtils::EXTENSION_ICONS[$matches[2]]."_16.svg";
                    if (!is_file($thumbfile)) {
                        $thumbfile = __DIR__."/../assets/icns/link_any_16.svg";
                    }
                    $response = new Response(file_get_contents($thumbfile) ?: null);
                    $response->headers->set('Cache-Control', 'max-age=86400');
                    $response->headers->set('Content-Type', 'image/svg+xml');
                    return $response;
                }
            }
        }
        throw new NotFoundHttpException("File thumb failed (not migrated)");
    }
}
