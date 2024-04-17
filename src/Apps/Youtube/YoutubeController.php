<?php

namespace Olz\Apps\Youtube;

use Olz\Apps\Youtube\Components\OlzYoutube\OlzYoutube;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class YoutubeController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/apps/youtube')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $html_out = OlzYoutube::render([]);
        return new Response($html_out);
    }
}
