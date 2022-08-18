<?php

namespace Olz\Controller;

use Olz\Components\OlzAppsList\OlzAppsList;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\AuthUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppsController extends AbstractController {
    #[Route('/apps/')]
    public function index(
        // TODO: Enable symfony-style dependency injection
        // AuthUtils $auth_utils,
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $auth_utils = AuthUtils::fromEnv();
        $user = $auth_utils->getAuthenticatedUser();
        $out = '';
        $out .= OlzHeader::render([]);
        $out .= "<div id='content_double'>";
        $out .= "<h1>Apps</h1>";
        $out .= OlzAppsList::render();
        $out .= "</div>";
        $out .= OlzFooter::render([]);

        return new Response($out);
    }
}
