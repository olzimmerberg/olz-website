<?php

namespace Olz\Apps\Panini2024;

use Olz\Apps\Panini2024\Components\OlzPanini2024\OlzPanini2024;
use Olz\Apps\Panini2024\Components\OlzPanini2024All\OlzPanini2024All;
use Olz\Apps\Panini2024\Components\OlzPanini2024Masks\OlzPanini2024Masks;
use Olz\Apps\Panini2024\Utils\Panini2024Utils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Panini2024Controller extends AbstractController {
    #[Route('/apps/panini24')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $html_out = OlzPanini2024::render([]);
        return new Response($html_out);
    }

    #[Route('/apps/panini24/all')]
    public function all(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $html_out = OlzPanini2024All::render([]);
        return new Response($html_out);
    }

    #[Route('/apps/panini24/mask/{mask}')]
    public function masks(
        Request $request,
        LoggerInterface $logger,
        string $mask,
    ): Response {
        $html_out = OlzPanini2024Masks::render(['mask' => $mask]);
        return new Response($html_out);
    }

    #[Route('/apps/panini24/single/{id}.jpg', requirements: ['id' => '\d+'])]
    public function single(
        Request $request,
        LoggerInterface $logger,
        int $id,
    ): Response {
        $out = Panini2024Utils::fromEnv()->renderSingle($id);
        $response = new Response($out);
        $response->headers->set('Content-Type', 'image/jpeg');
        $response->headers->set('Content-Disposition', "attachment; filename=panini-{$id}.jpg");
        return $response;
    }
}
