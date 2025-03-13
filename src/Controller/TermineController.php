<?php

namespace Olz\Controller;

use Olz\Termine\Components\OlzICal\OlzICal;
use Olz\Termine\Components\OlzTerminDetail\OlzTerminDetail;
use Olz\Termine\Components\OlzTermineList\OlzTermineList;
use Olz\Termine\Components\OlzTerminLocationDetail\OlzTerminLocationDetail;
use Olz\Termine\Components\OlzTerminLocationsList\OlzTerminLocationsList;
use Olz\Termine\Components\OlzTerminTemplateDetail\OlzTerminTemplateDetail;
use Olz\Termine\Components\OlzTerminTemplatesList\OlzTerminTemplatesList;
use Olz\Utils\HttpUtils;
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
        HttpUtils $httpUtils,
        OlzTermineList $olzTermineList,
    ): Response {
        $httpUtils->countRequest($request, ['filter', 'von']);
        $out = $olzTermineList->getHtml([]);
        return new Response($out);
    }

    #[Route('/termine/{id}', requirements: ['id' => '\d+'])]
    public function termineDetail(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzTerminDetail $olzTerminDetail,
        int $id,
    ): Response {
        $httpUtils->countRequest($request, ['von']);
        $out = $olzTerminDetail->getHtml(['id' => $id]);
        return new Response($out);
    }

    #[Route('/termine/orte')]
    public function terminLocationsList(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzTerminLocationsList $olzTerminLocationsList,
    ): Response {
        $httpUtils->countRequest($request);
        $out = $olzTerminLocationsList->getHtml([]);
        return new Response($out);
    }

    #[Route('/termine/orte/{id}', requirements: ['id' => '\d+'])]
    public function terminLocationDetail(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzTerminLocationDetail $olzTerminLocationDetail,
        int $id,
    ): Response {
        $httpUtils->countRequest($request);
        $out = $olzTerminLocationDetail->getHtml(['id' => $id]);
        return new Response($out);
    }

    #[Route('/termine/vorlagen')]
    public function terminTemplatesList(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzTerminTemplatesList $olzTerminTemplatesList,
    ): Response {
        $httpUtils->countRequest($request);
        $out = $olzTerminTemplatesList->getHtml([]);
        return new Response($out);
    }

    #[Route('/termine/vorlagen/{id}', requirements: ['id' => '\d+'])]
    public function terminTemplateDetail(
        Request $request,
        LoggerInterface $logger,
        HttpUtils $httpUtils,
        OlzTerminTemplateDetail $olzTerminTemplateDetail,
        int $id,
    ): Response {
        $httpUtils->countRequest($request);
        $out = $olzTerminTemplateDetail->getHtml(['id' => $id]);
        return new Response($out);
    }

    #[Route('/olz_ical.ics')]
    public function termineICal(
        Request $request,
        LoggerInterface $logger,
        OlzICal $olzICal,
    ): Response {
        $out = $olzICal->getHtml([]);
        $response = new Response($out);
        $response->headers->set('Content-Type', 'text/calendar');
        return $response;
    }
}
