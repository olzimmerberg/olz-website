<?php

namespace Olz\Controller;

use Olz\Api\OlzApi;
use Olz\Utils\EnvUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController {
    #[Route('/api/{endpoint_name}')]
    public function index(
        Request $request,
        LoggerInterface $logger,
        string $endpoint_name
    ): JsonResponse {
        $olz_api = OlzApi::getInstance();

        $env_utils = EnvUtils::fromEnv();
        $logger = $env_utils->getLogsUtils()->getLogger('OlzApi');
        $olz_api->setLogger($logger);

        $request->server->set('PATH_INFO', "/{$endpoint_name}");
        return $olz_api->getResponse($request);
    }
}
