<?php

namespace Olz\Controller;

use Olz\Termine\Components\OlzTerminDetail\OlzTerminDetail;
use Olz\Termine\Components\OlzTermineList\OlzTermineList;
use Olz\Termine\Components\OlzTerminLocationDetail\OlzTerminLocationDetail;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TermineController extends AbstractController {
    #[Route('/termine')]
    public function termineList(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $out = OlzTermineList::render([]);
        return new Response($out);
    }

    #[Route('/termine/{id}', requirements: ['id' => '\d+'])]
    public function termineDetail(
        Request $request,
        LoggerInterface $logger,
        int $id,
    ): Response {
        $out = OlzTerminDetail::render(['id' => $id]);
        return new Response($out);
    }

    #[Route('/termine/orte/{id}', requirements: ['id' => '\d+'])]
    public function terminLocationDetail(
        Request $request,
        LoggerInterface $logger,
        int $id,
    ): Response {
        $out = OlzTerminLocationDetail::render(['id' => $id]);
        return new Response($out);
    }
}
