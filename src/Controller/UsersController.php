<?php

namespace Olz\Controller;

use Olz\Users\Components\OlzUserDetail\OlzUserDetail;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/benutzer')]
    public function users(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = 'TODO';
        return new Response($out);
    }

    #[Route('/benutzer/ich')]
    public function me(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzUserDetail::render([
            'id' => $this->authUtils()->getCurrentUser()?->getId(),
        ]);
        return new Response($out);
    }

    #[Route('/benutzer/{id}', requirements: ['id' => '\d+'])]
    public function user(
        Request $request,
        LoggerInterface $logger,
        int $id,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzUserDetail::render(['id' => $id]);
        return new Response($out);
    }
}
