<?php

namespace Olz\Apps\Quiz;

use Olz\Apps\Quiz\Components\OlzQuiz\OlzQuiz;
use Olz\Utils\HttpUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuizController extends AbstractController {
    #[Route('/apps/quiz')]
    public function index(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzQuiz $olzQuiz,
    ): Response {
        $httpUtils->countRequest($request);
        $html_out = $olzQuiz->getHtml([]);
        return new Response($html_out);
    }
}
