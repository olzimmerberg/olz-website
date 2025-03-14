<?php

namespace Olz\Controller;

use Olz\Api\OlzApi;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController {
    #[Route('/api/{endpoint_name}')]
    public function index(
        Request $request,
        LoggerInterface $log,
        HttpUtils $httpUtils,
        OlzApi $olz_api,
        string $endpoint_name
    ): Response {
        $httpUtils->countRequest($request);

        $olz_api->setLogger($log);

        $request->server->set('PATH_INFO', "/{$endpoint_name}");
        return $olz_api->getResponse($request);
    }
}
