<?php

namespace Olz\Controller;

use Olz\Apps\OlzApps;
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
        $out .= "<div style='display:flex; flex-wrap:wrap;'>";
        $out .= implode('<br>', array_map(function ($app) {
            $icon = $app->getIcon();
            $display_name = $app->getDisplayName();
            $href = $app->getHref();
            $basename = $app->getBasename();
            return <<<ZZZZZZZZZZ
            <a href='{$href}'>
                <div style='width:250px;text-align:center;'>
                    <img src='{$icon}' alt='{$basename}-icon' class='noborder' style='max-width:100px;' />
                    <div>{$display_name}</div>
                </div>
            </a>
            ZZZZZZZZZZ;
        }, OlzApps::getAppsForUser($user)));
        $out .= "</div>";
        $out .= "<div style='margin-top:100px;'>Einige Icons mit Emojis von <a href='https://openmoji.org/' target='_blank'>OpenMoji</a> &mdash; dem open-source Emoji- und Icon-Projekt. Lizenz: <a href='https://creativecommons.org/licenses/by-sa/4.0/#' target='_blank'>CC BY-SA 4.0</a></div>";
        $out .= "</div>";
        $out .= OlzFooter::render([]);

        return new Response($out);
    }
}
