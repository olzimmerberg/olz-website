<?php

namespace Olz\Apps\Panini2024;

use Olz\Apps\Panini2024\Components\OlzPanini2024\OlzPanini2024;
use Olz\Apps\Panini2024\Components\OlzPanini2024All\OlzPanini2024All;
use Olz\Apps\Panini2024\Components\OlzPanini2024Masks\OlzPanini2024Masks;
use Olz\Apps\Panini2024\Utils\Panini2024Utils;
use Olz\Entity\Panini2024\Panini2024Picture;
use Olz\Utils\WithUtilsTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Panini2024Controller extends AbstractController {
    use WithUtilsTrait;

    #[Route('/apps/panini24')]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $html_out = OlzPanini2024::render([]);
        return new Response($html_out);
    }

    #[Route('/apps/panini24/all')]
    public function all(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $html_out = OlzPanini2024All::render([]);
        return new Response($html_out);
    }

    #[Route('/apps/panini24/mask/{mask}')]
    public function masks(
        Request $request,
        LoggerInterface $logger,
        string $mask,
    ): Response {
        $html_out = OlzPanini2024Masks::render(['mask' => $mask]);
        return new Response($html_out);
    }

    #[Route('/apps/panini24/single/{id}.jpg', requirements: ['id' => '\d+'])]
    public function single(
        Request $request,
        LoggerInterface $logger,
        int $id,
    ): Response {
        $out = Panini2024Utils::fromEnv()->renderSingle($id);
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
        ini_set('memory_limit', '500M');
        set_time_limit(4000);

        $pdf_out = null;
        [$pages, $options] = $this->parseSpec($spec, /* num_per_page= */ 12);
        $pdf_out = Panini2024Utils::fromEnv()->render3x5Pages($pages, $options);
        if (!$pdf_out) {
            return new Response("Must adhere to spec: (random-N | ID,ID,ID,ID,ID,ID,ID,ID,ID,ID,ID,ID) [-grid]");
        }
        $response = new Response($pdf_out);
        $response->headers->set('Content-Type', 'application/pdf');
        return $response;
    }

    #[Route('/apps/panini24/pdf/4x4/{spec}.pdf')]
    public function pdf4x4(
        Request $request,
        LoggerInterface $logger,
        string $spec,
    ): Response {
        ini_set('memory_limit', '500M');
        set_time_limit(4000);

        $pdf_out = null;
        [$pages, $options] = $this->parseSpec($spec, /* num_per_page= */ 16);
        $pdf_out = Panini2024Utils::fromEnv()->render4x4Pages($pages, $options);
        if (!$pdf_out) {
            return new Response("Must adhere to spec: (random-N | ID,ID,ID,ID,ID,ID,ID,ID,ID,ID,ID,ID,ID,ID,ID,ID) [-grid]");
        }
        $response = new Response($pdf_out);
        $response->headers->set('Content-Type', 'application/pdf');
        return $response;
    }

    private function parseSpec($spec, $num_per_page) {
        $random_res = preg_match('/^random-([0-9]+)(-grid)?$/i', $spec, $random_matches);
        if ($random_res) {
            $num = intval($random_matches[1]);
            $options = [
                'grid' => ($random_matches[2] ?? '') === '-grid',
            ];
            $panini_repo = $this->entityManager()->getRepository(Panini2024Picture::class);
            $all_ids = array_map(function ($picture) {
                return $picture->getId();
            }, $panini_repo->findAll());
            $ids_len = count($all_ids);
            $pages = [];
            for ($p = 0; $p < $num; $p++) {
                $ids = [];
                for ($i = 0; $i < 16; $i++) {
                    $ids[] = $all_ids[random_int(0, $ids_len - 1)];
                }
                $pages[] = ['ids' => $ids];
            }
            return [$pages, $options];
        }
        $duplicate_res = preg_match('/^duplicate-([0-9]+)(-grid)?$/i', $spec, $duplicate_matches);
        if ($duplicate_res) {
            $id = intval($duplicate_matches[1]);
            $ids = [];
            for ($i = 0; $i < $num_per_page; $i++) {
                $ids[] = $id;
            }
            $options = [
                'grid' => ($duplicate_matches[2] ?? '') === '-grid',
            ];
            $pages = [
                ['ids' => $ids],
            ];
            return [$pages, $options];
        }
        $pattern_param = $num_per_page - 1;
        $pattern = "/^((?:[0-9]+,){{$pattern_param}}[0-9]+)(-grid)?$/i";
        $list_res = preg_match($pattern, $spec, $list_matches);
        if ($list_res) {
            $ids = array_map(function ($idstr) {
                return intval($idstr);
            }, explode(',', $list_matches[1]));
            $options = [
                'grid' => ($list_matches[2] ?? '') === '-grid',
            ];
            $pages = [
                ['ids' => $ids],
            ];
            return [$pages, $options];
        }
        throw new \Exception("Invalid spec: {$spec} ({$pattern})");
    }

    #[Route('/apps/panini24/pdf/olz.pdf')]
    public function pdfOlz(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        ini_set('memory_limit', '500M');
        set_time_limit(4000);

        $pdf_out = Panini2024Utils::fromEnv()->renderOlzPages();
        $response = new Response($pdf_out);
        $response->headers->set('Content-Type', 'application/pdf');
        return $response;
    }

    #[Route('/apps/panini24/pdf/history.pdf')]
    public function pdfHistory(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        ini_set('memory_limit', '500M');
        set_time_limit(4000);

        $pdf_out = Panini2024Utils::fromEnv()->renderHistoryPages();
        $response = new Response($pdf_out);
        $response->headers->set('Content-Type', 'application/pdf');
        return $response;
    }

    #[Route('/apps/panini24/pdf/dresses.pdf')]
    public function pdfDresses(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        ini_set('memory_limit', '500M');
        set_time_limit(4000);

        $pdf_out = Panini2024Utils::fromEnv()->renderDressesPages();
        $response = new Response($pdf_out);
        $response->headers->set('Content-Type', 'application/pdf');
        return $response;
    }

    #[Route('/apps/panini24/pdf/maps.pdf')]
    public function pdfMaps(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        ini_set('memory_limit', '500M');
        set_time_limit(4000);

        $pdf_out = Panini2024Utils::fromEnv()->renderMapsPages();
        $response = new Response($pdf_out);
        $response->headers->set('Content-Type', 'application/pdf');
        return $response;
    }

    #[Route('/apps/panini24/pdf/book.pdf')]
    public function pdfBook(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        ini_set('memory_limit', '500M');
        set_time_limit(4000);

        $pdf_out = Panini2024Utils::fromEnv()->renderBookPages();
        $response = new Response($pdf_out);
        $response->headers->set('Content-Type', 'application/pdf');
        return $response;
    }

    #[Route('/apps/panini24/pdf/back.pdf')]
    public function pdfBack(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        ini_set('memory_limit', '500M');
        set_time_limit(4000);

        $pdf_out = Panini2024Utils::fromEnv()->renderBackPages();
        $response = new Response($pdf_out);
        $response->headers->set('Content-Type', 'application/pdf');
        return $response;
    }
}
