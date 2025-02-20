<?php

namespace Olz\Apps\Files;

use Olz\Apps\Files\Components\OlzFiles\OlzFiles;
use Olz\Apps\Files\Components\OlzWebDav\OlzWebDav;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilesController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/apps/files')]
    public function index(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $html_out = OlzFiles::render();
        return new Response($html_out);
    }

    #[Route('/apps/files/webdav')]
    public function webdavIndex(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        return $this->webdav($request, $logger);
    }

    #[Route('/apps/files/webdav/{path}', requirements: ['path' => '.*'])]
    public function webdavPath(
        Request $request,
        LoggerInterface $logger,
        string $path,
    ): Response {
        $this->httpUtils()->countRequest($request);
        return $this->webdav($request, $logger, $path);
    }

    protected function webdav(
        Request $request,
        LoggerInterface $logger,
        ?string $path = null,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $html_out = OlzWebDav::render(['path' => $path]);
        $response = new Response($html_out);
        foreach (headers_list() as $header) {
            $colon_position = strpos($header, ':');
            if ($colon_position !== false) {
                $key = substr($header, 0, $colon_position);
                $value = substr($header, $colon_position + 1);
                $response->headers->set($key, $value);
            }
        }
        return $response;
    }
}
