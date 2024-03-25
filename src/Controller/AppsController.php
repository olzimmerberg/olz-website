<?php

namespace Olz\Controller;

use Olz\Apps\OlzApps;
use Olz\Components\Apps\OlzAppsList\OlzAppsList;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\AuthUtils;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppsController extends AbstractController {
    public static $title = "Apps";
    public static $description = "Eine Sammlung von kleineren Tools rund um die Tätigkeiten der OL Zimmerberg.";

    #[Route('/apps/')]
    public function index(
        AuthUtils $auth_utils,
        Request $request,
        LoggerInterface $logger,
    ): Response {
        HttpUtils::fromEnv()->validateGetParams([]);

        $out = OlzHeader::render([
            'title' => self::$title,
            'description' => self::$description,
        ]);
        $out .= "<div class='content-full'>";
        $out .= "<h1>Apps</h1>";
        $out .= OlzAppsList::render();
        $out .= "</div>";
        $out .= OlzFooter::render([]);

        return new Response($out);
    }

    #[Route('/apps/{app}/icon.{ext}')]
    public function icon(
        string $app,
        string $ext,
        AuthUtils $auth_utils,
        Request $request,
        LoggerInterface $logger,
    ): Response {
        HttpUtils::fromEnv()->validateGetParams([]);

        $metadata = OlzApps::getApp($app);
        $icon_path = $metadata?->getIconPath();
        if (!$icon_path || !is_file($icon_path)) {
            $response = new Response(file_get_contents(__DIR__.'/../Apps/default_icon.svg'));
            $response->headers->set('Cache-Control', 'max-age=2592000');
            $response->headers->set('Content-Type', 'image/svg+xml');
            return $response;
        }
        $response = new Response(file_get_contents($icon_path));
        $response->headers->set('Cache-Control', 'max-age=2592000');
        $response->headers->set('Content-Type', $ext === 'svg' ? 'image/svg+xml' : 'image/png');
        return $response;
    }
}
