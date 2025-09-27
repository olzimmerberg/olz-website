<?php

namespace Olz\Apps\Panini2024;

use Olz\Apps\Panini2024\Components\OlzPanini2024\OlzPanini2024;
use Olz\Apps\Panini2024\Components\OlzPanini2024All\OlzPanini2024All;
use Olz\Apps\Panini2024\Components\OlzPanini2024Masks\OlzPanini2024Masks;
use Olz\Apps\Panini2024\Utils\Panini2024UtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Panini2024Controller extends AbstractController {
    use Panini2024UtilsTrait;

    #[Route('/apps/panini24')]
    public function index(
        Request $request,
        LoggerInterface $logger,
        OlzPanini2024 $olzPanini2024,
    ): Response {
        $html_out = $olzPanini2024->getHtml([]);
        return new Response($html_out);
    }

    #[Route('/apps/panini24/all')]
    public function all(
        Request $request,
        LoggerInterface $logger,
        OlzPanini2024All $olzPanini2024All,
    ): Response {
        $html_out = $olzPanini2024All->getHtml([]);
        return new Response($html_out);
    }

    #[Route('/apps/panini24/mask/{mask}')]
    public function masks(
        Request $request,
        LoggerInterface $logger,
        OlzPanini2024Masks $olzPanini2024Masks,
        string $mask,
    ): Response {
        $html_out = $olzPanini2024Masks->getHtml(['mask' => $mask]);
        return new Response($html_out);
    }

    #[Route('/apps/panini24/single/{id}.jpg', requirements: ['id' => '\d+'])]
    public function single(
        Request $request,
        LoggerInterface $logger,
        int $id,
    ): Response {
        $out = $this->paniniUtils()->renderSingle($id);
        $response = new Response($out);
        $response->headers->set('Content-Type', 'image/jpeg');
        return $response;
    }

    #[Route('/apps/panini24/pdf/3x5/{spec}.pdf')]
    public function pdf3x5(
        Request $request,
        LoggerInterface $logger,
        string $spec,
    ): Response {
        $this->setLimits();
        [$pages, $options] = $this->paniniUtils()->parseSpec($spec, /* num_per_page= */ 12);
        $pdf_out = $this->paniniUtils()->render3x5Pages($pages, $options);
        if (!$pdf_out) {
            return new Response("Must adhere to spec: (random-N | ID,ID,ID,ID,ID,ID,ID,ID,ID,ID,ID,ID) [-grid]");
        }
        return $this->pdfResponse($pdf_out);
    }

    #[Route('/apps/panini24/pdf/4x4/{spec}.pdf')]
    public function pdf4x4(
        Request $request,
        LoggerInterface $logger,
        string $spec,
    ): Response {
        $this->setLimits();
        [$pages, $options] = $this->paniniUtils()->parseSpec($spec, /* num_per_page= */ 16);
        $pdf_out = $this->paniniUtils()->render4x4Pages($pages, $options);
        if (!$pdf_out) {
            return new Response("Must adhere to spec: (random-N | ID,ID,ID,ID,ID,ID,ID,ID,ID,ID,ID,ID,ID,ID,ID,ID) [-grid]");
        }
        return $this->pdfResponse($pdf_out);
    }

    #[Route('/apps/panini24/pdf/4x4/{spec}.zip')]
    public function zipDuplicatesGrid4x4(
        Request $request,
        LoggerInterface $logger,
        string $spec,
    ): Response {
        $this->setLimits();
        if ($spec !== 'duplicates' && $spec !== 'duplicates-grid') {
            return new Response("Must be 'duplicates' or 'duplicates-grid'");
        }
        $options = [
            'grid' => $spec === 'duplicates-grid',
        ];
        $zip_out = $this->paniniUtils()->render4x4Zip($options);
        $response = new Response($zip_out);
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Content-Disposition', "attachment;filename={$spec}.zip");
        return $response;
    }

    #[Route('/apps/panini24/pdf/olz.pdf')]
    public function pdfOlz(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->setLimits();
        $pdf_out = $this->paniniUtils()->renderOlzPages();
        return $this->pdfResponse($pdf_out);
    }

    #[Route('/apps/panini24/pdf/history.pdf')]
    public function pdfHistory(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->setLimits();
        $pdf_out = $this->paniniUtils()->renderHistoryPages();
        return $this->pdfResponse($pdf_out);
    }

    #[Route('/apps/panini24/pdf/dresses.pdf')]
    public function pdfDresses(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->setLimits();
        $pdf_out = $this->paniniUtils()->renderDressesPages();
        return $this->pdfResponse($pdf_out);
    }

    #[Route('/apps/panini24/pdf/maps.pdf')]
    public function pdfMaps(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->setLimits();
        $pdf_out = $this->paniniUtils()->renderMapsPages();
        return $this->pdfResponse($pdf_out);
    }

    #[Route('/apps/panini24/pdf/book.pdf')]
    public function pdfBook(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->setLimits();
        $pdf_out = $this->paniniUtils()->renderBookPages();
        return $this->pdfResponse($pdf_out);
    }

    #[Route('/apps/panini24/pdf/back.pdf')]
    public function pdfBack(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $this->setLimits();
        $pdf_out = $this->paniniUtils()->renderBackPages();
        return $this->pdfResponse($pdf_out);
    }

    private function setLimits(): void {
        ini_set('memory_limit', '500M');
        set_time_limit(4000);
    }

    private function pdfResponse(string $pdf_out): Response {
        $response = new Response($pdf_out);
        $response->headers->set('Content-Type', 'application/pdf');
        return $response;
    }
}
