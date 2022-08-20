<?php

namespace Olz\Apps\Quiz;

use Olz\Apps\Quiz\Components\OlzQuiz\OlzQuiz;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuizController extends AbstractController {
    #[Route('/apps/quiz')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $html_out = OlzQuiz::render([]);
        return new Response($html_out);
    }
}
