<?php

namespace Olz\Controller;

use Olz\Termine\Components\OlzICal\OlzICal;
use Olz\Termine\Components\OlzTerminDetail\OlzTerminDetail;
use Olz\Termine\Components\OlzTermineList\OlzTermineList;
use Olz\Termine\Components\OlzTerminLocationDetail\OlzTerminLocationDetail;
use Olz\Termine\Components\OlzTerminLocationsList\OlzTerminLocationsList;
use Olz\Termine\Components\OlzTerminTemplateDetail\OlzTerminTemplateDetail;
use Olz\Termine\Components\OlzTerminTemplatesList\OlzTerminTemplatesList;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TermineController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/termine')]
    public function termineList(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request, ['filter']);
        $out = OlzTermineList::render([]);
        return new Response($out);
    }

    #[Route('/termine/{id}', requirements: ['id' => '\d+'])]
    public function termineDetail(
        Request $request,
        LoggerInterface $logger,
        int $id,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzTerminDetail::render(['id' => $id]);
        return new Response($out);
    }

    #[Route('/termine/orte')]
    public function terminLocationsList(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzTerminLocationsList::render([]);
        return new Response($out);
    }

    #[Route('/termine/orte/{id}', requirements: ['id' => '\d+'])]
    public function terminLocationDetail(
        Request $request,
        LoggerInterface $logger,
        int $id,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzTerminLocationDetail::render(['id' => $id]);
        return new Response($out);
    }

    #[Route('/termine/vorlagen')]
    public function terminTemplatesList(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzTerminTemplatesList::render([]);
        return new Response($out);
    }

    #[Route('/termine/vorlagen/{id}', requirements: ['id' => '\d+'])]
    public function terminTemplateDetail(
        Request $request,
        LoggerInterface $logger,
        int $id,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzTerminTemplateDetail::render(['id' => $id]);
        return new Response($out);
    }

    #[Route('/olz_ical.ics')]
    public function termineICal(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $out = OlzICal::render();
        $response = new Response($out);
        $response->headers->set('Content-Type', 'text/calendar');
        return $response;
    }
}
