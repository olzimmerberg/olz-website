<?php

namespace Olz\Apps\Files;

use Olz\Apps\Files\Components\OlzFiles\OlzFiles;
use Olz\Apps\Files\Components\OlzWebDav\OlzWebDav;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilesController extends AbstractController {
    #[Route('/apps/files')]
    public function index(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzFiles $olzFiles,
    ): Response {
        $httpUtils->countRequest($request);
        $html_out = $olzFiles->getHtml([]);
        return new Response($html_out);
    }

    #[Route('/apps/files/webdav')]
    public function webdavIndex(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzWebDav $olzWebDav,
    ): Response {
        $httpUtils->countRequest($request);
        return $this->webdav($request, $logger, $olzWebDav);
    }

    #[Route('/apps/files/webdav/{path}', requirements: ['path' => '.*'])]
    public function webdavPath(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzWebDav $olzWebDav,
        string $path,
    ): Response {
        $httpUtils->countRequest($request);
        return $this->webdav($request, $logger, $olzWebDav, $path);
    }

    protected function webdav(
        Request $request,
        LoggerInterface $logger,
        OlzWebDav $olzWebDav,
        ?string $path = null,
    ): Response {
        $html_out = $olzWebDav->getHtml(['path' => $path]);
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
