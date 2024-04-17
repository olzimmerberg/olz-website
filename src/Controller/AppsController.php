<?php

namespace Olz\Controller;

use Olz\Components\Apps\OlzAppsList\OlzAppsList;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\AuthUtils;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppsController extends AbstractController {
    use WithUtilsTrait;

    public static $title = "Apps";
    public static $description = "Eine Sammlung von kleineren Tools rund um die Tätigkeiten der OL Zimmerberg.";

    #[Route('/apps/')]
    public function index(
        AuthUtils $auth_utils,
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->validateGetParams([]);
        $this->httpUtils()->countRequest($request);

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
}
