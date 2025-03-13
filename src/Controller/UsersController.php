<?php

namespace Olz\Controller;

use Olz\Users\Components\OlzUserDetail\OlzUserDetail;
use Olz\Utils\AuthUtils;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController {
    #[Route('/benutzer')]
    public function users(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
    ): Response {
        $httpUtils->countRequest($request);
        $out = 'TODO';
        return new Response($out);
    }

    #[Route('/benutzer/ich')]
    public function me(
        Request $request,
        LoggerInterface $logger,
        AuthUtils $authUtils,
        HttpUtils $httpUtils,
        OlzUserDetail $olzUserDetail,
    ): Response {
        $httpUtils->countRequest($request);
        $out = $olzUserDetail->getHtml([
            'id' => $authUtils->getCurrentUser()?->getId(),
        ]);
        return new Response($out);
    }

    #[Route('/benutzer/{id}', requirements: ['id' => '\d+'])]
    public function user(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzUserDetail $olzUserDetail,
        int $id,
    ): Response {
        $httpUtils->countRequest($request);
        $out = $olzUserDetail->getHtml(['id' => $id]);
        return new Response($out);
    }
}
