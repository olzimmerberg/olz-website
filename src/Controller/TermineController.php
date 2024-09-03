<?php

namespace Olz\Controller;

use Olz\Entity\Termine\Termin;
use Olz\Entity\Termine\TerminLocation;
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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class TermineController extends AbstractController {
    use WithUtilsTrait;

    #[Route('/termine')]
    public function termineList(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzTermineList::render([]);
        return new Response($out);
    }

    #[Route('/termine/{ident}', requirements: ['ident' => '[^/]+'])]
    public function termineDetail(
        Request $request,
        LoggerInterface $logger,
        string $ident,
    ): Response {
        if ($ident === 'vorlagen') {
            return new RedirectResponse("/termin_vorlagen", 301, ['X-OLZ-Redirect' => 'termineDetailVorlagen']);
        }
        if ($ident === 'orte') {
            return new RedirectResponse("/termin_orte", 301, ['X-OLZ-Redirect' => 'termineDetailOrte']);
        }
        $this->httpUtils()->countRequest($request);
        $termin_repo = $this->entityManager()->getRepository(Termin::class);
        $termin = $termin_repo->findOneByIdent($ident);
        if (!$termin) {
            throw new NotFoundHttpException();
        }
        $out = OlzTerminDetail::render(['termin' => $termin]);
        return new Response($out);
    }

    #[Route('/termin_orte')]
    public function terminLocationsList(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzTerminLocationsList::render([]);
        return new Response($out);
    }

    #[Route('/termin_orte/{ident}', requirements: ['ident' => '\S+'])]
    public function terminLocationDetail(
        Request $request,
        LoggerInterface $logger,
        string $ident,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $termin_location_repo = $this->entityManager()->getRepository(TerminLocation::class);
        $termin_location = $termin_location_repo->findOneByIdent($ident);
        if (!$termin_location) {
            throw new NotFoundHttpException();
        }
        $out = OlzTerminLocationDetail::render(['termin_location' => $termin_location]);
        return new Response($out);
    }

    #[Route('/termine/orte/{id}', requirements: ['id' => '\d+'])]
    public function terminLocationDetailOld(
        Request $request,
        LoggerInterface $logger,
        int $id,
    ): RedirectResponse {
        $this->httpUtils()->countRequest($request);
        $termin_location_repo = $this->entityManager()->getRepository(TerminLocation::class);
        $termin_location = $termin_location_repo->findOneBy(['id' => $id]);
        if (!$termin_location) {
            throw new NotFoundHttpException();
        }
        return new RedirectResponse("/termin_orte/{$termin_location->getIdent()}", 301, ['X-OLZ-Redirect' => 'terminLocationDetailOld']);
    }

    #[Route('/termin_vorlagen')]
    public function terminTemplatesList(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzTerminTemplatesList::render([]);
        return new Response($out);
    }

    #[Route('/termin_vorlagen/{id}', requirements: ['id' => '\d+'])]
    public function terminTemplateDetail(
        Request $request,
        LoggerInterface $logger,
        int $id,
    ): Response {
        $this->httpUtils()->countRequest($request);
        $out = OlzTerminTemplateDetail::render(['id' => $id]);
        return new Response($out);
    }

    #[Route('/termine/vorlagen/{id}', requirements: ['id' => '\d+'])]
    public function terminTemplateDetailOld(
        Request $request,
        LoggerInterface $logger,
        int $id,
    ): RedirectResponse {
        $this->httpUtils()->countRequest($request);
        return new RedirectResponse("/termin_vorlagen/{$id}", 301, ['X-OLZ-Redirect' => 'terminTemplateDetailOld']);
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
