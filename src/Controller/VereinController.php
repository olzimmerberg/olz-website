<?php

namespace Olz\Controller;

use Olz\Roles\Components\OlzRoleDetail\OlzRoleDetail;
use Olz\Roles\Components\OlzVerein\OlzVerein;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VereinController extends AbstractController {
    #[Route('/verein')]
    public function verein(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzVerein $olzVerein,
    ): Response {
        $httpUtils->countRequest($request, ['von']);
        $httpUtils->stripParams($request, ['von']);
        $out = $olzVerein->getHtml([]);
        return new Response($out);
    }

    #[Route('/verein/{ressort}')]
    public function ressort(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzRoleDetail $olzRoleDetail,
        string $ressort,
    ): Response {
        $httpUtils->countRequest($request, ['von']);
        $httpUtils->stripParams($request, ['von']);
        $out = $olzRoleDetail->getHtml(['ressort' => $ressort]);
        return new Response($out);
    }
}
