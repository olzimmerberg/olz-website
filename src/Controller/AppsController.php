<?php

namespace Olz\Controller;

use Olz\Apps\OlzApps;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AppsController extends AbstractController {
    #[Route('/apps/{app_basename}/icon.svg', requirements: ['app_basename' => '[a-zA-Z0-9_-]+'])]
    public function appIcon(
        string $app_basename,
    ): Response {
        $app = OlzApps::getApp($app_basename);
        $path = $app?->getIconPath();
        if ($path === null) {
            throw new NotFoundHttpException();
        }
        return new BinaryFileResponse($path);
    }
}
