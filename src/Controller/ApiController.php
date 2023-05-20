<?php

namespace Olz\Controller;

use Olz\Api\OlzApi;
use Olz\Utils\LogsUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController {
    #[Route('/api/{endpoint_name}')]
    public function index(
        Request $request,
        LoggerInterface $logger,
        string $endpoint_name
    ): Response {
        $olz_api = OlzApi::getInstance();

        $logger = LogsUtils::fromEnv()->getLogger('OlzApi');
        $olz_api->setLogger($logger);

        $request->server->set('PATH_INFO', "/{$endpoint_name}");
        return $olz_api->getResponse($request);
    }

    // TODO: Remove!
    #[Route('/api/index.php/{endpoint_name}')]
    public function oldIndex(
        Request $request,
        LoggerInterface $logger,
        string $endpoint_name
    ): Response {
        $olz_api = OlzApi::getInstance();

        $logger = LogsUtils::fromEnv()->getLogger('OlzApi');
        $olz_api->setLogger($logger);

        $request->server->set('PATH_INFO', "/{$endpoint_name}");
        return $olz_api->getResponse($request);
    }
}
