<?php

namespace Olz\Apps\Files;

use Olz\Apps\Files\Components\OlzFiles\OlzFiles;
use Olz\Apps\Files\Components\OlzWebDav\OlzWebDav;
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
    ): Response {
        $html_out = OlzFiles::render();
        return new Response($html_out);
    }

    #[Route('/apps/files/webdav')]
    public function webdavIndex(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        return $this->webdav($request, $logger);
    }

    #[Route('/apps/files/webdav/{path}', requirements: ['path' => '.*'])]
    public function webdavPath(
        Request $request,
        LoggerInterface $logger,
        string $path,
    ): Response {
        return $this->webdav($request, $logger, $path);
    }

    protected function webdav(
        Request $request,
        LoggerInterface $logger,
        ?string $path = null,
    ): Response {
        $html_out = OlzWebDav::render(['path' => $path]);
        $response = new Response($html_out);
        foreach (headers_list() as $header) {
            $colon_position = strpos($header, ':');
            $key = substr($header, 0, $colon_position);
            $value = substr($header, $colon_position + 1);
            $response->headers->set($key, $value);
        }
        return $response;
    }
}
