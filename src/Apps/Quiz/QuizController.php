<?php

namespace Olz\Apps\Quiz;

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
        return new Response('WTF');
    }
}
