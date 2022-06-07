<?php

namespace Olz\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController {
    #[Route('/test')]
    public function get(
        LoggerInterface $logger,
    ): Response {
        $logger->info("symfony-TestController was invoked.");
        $text = <<<'ZZZZZZZZZZ'
        <h1>symfony-TestController</h1>
        ZZZZZZZZZZ;
        return new Response("{$text}");
    }
}
