<?php

namespace Olz\Controller;

use Olz\Api\OlzApi;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/api/{endpoint_name}')]
    public function index(
        Request $request,
        LoggerInterface $logger,
        OlzApi $olz_api,
        string $endpoint_name
    ): Response {
        $this->httpUtils()->countRequest($request);

        $olz_api->setLogger($this->log());

        $request->server->set('PATH_INFO', "/{$endpoint_name}");
        return $olz_api->getResponse($request);
    }
}
