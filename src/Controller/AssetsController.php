<?php

namespace Olz\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AssetsController extends AbstractController {
    #[Route('/assets/icns/')]
    public function assetsIcnsIndex(
        Request $request,
        LoggerInterface $logger,
    ): Response {
        $assets_path = __DIR__.'/../../assets';
        $icns_entries = scandir("{$assets_path}/icns");
        $svg_icons = [];
        foreach ($icns_entries as $entry) {
            $is_svg_icon = preg_match('/^([a-zA-Z0-9_]+)_([0-9]+)\.svg$/i', $entry, $matches);
            if (!$is_svg_icon) {
                continue;
            }
            $icon_name = $matches[1];
            $icon_size = $matches[2];
            if (!isset($svg_icons[$icon_name])) {
                $svg_icons[$icon_name] = [];
            }
            $svg_icons[$icon_name][$icon_size] = $entry;
        }
        $out = '';
        foreach ($svg_icons as $icon_name => $icon_by_size) {
            $out .= "<div><h2>{$icon_name}</h2>";
            foreach ($icon_by_size as $icon_size => $icon) {
                $out .= "<div><h3>{$icon_size}</h3>";
                $original_size = intval($icon_size);
                $double_size = $original_size * 2;
                $triple_size = $original_size * 3;
                $icon_href = "/assets/icns/{$icon}";
                $out .= "<img src='{$icon_href}' style='width:{$original_size}px; margin:1px; border:1px solid black;'/>";
                $out .= "<img src='{$icon_href}' style='width:{$double_size}px; margin:1px; border:1px solid black;'/>";
                $out .= "<img src='{$icon_href}' style='width:{$triple_size}px; margin:1px; border:1px solid black;'/>";

                $out .= "<div style='display:inline-block; background-color:rgb(200,200,200);'>";
                $out .= "<img src='{$icon_href}' style='width:{$original_size}px; margin:1px; border:1px solid black;'/>";
                $out .= "<img src='{$icon_href}' style='width:{$double_size}px; margin:1px; border:1px solid black;'/>";
                $out .= "<img src='{$icon_href}' style='width:{$triple_size}px; margin:1px; border:1px solid black;'/>";
                $out .= "</div>";

                $out .= "</div>";
            }
            $out .= "</div>";
        }
        return new Response($out);
    }

    #[Route('/assets/{folder}/{filename}.{ext}', requirements: [
        'folder' => '[a-zA-Z0-9_-]+',
        'filename' => '[a-zA-Z0-9_-]+',
        'ext' => '[a-zA-Z0-9_-]+',
    ])]
    public function folderAsset(
        Request $request,
        LoggerInterface $logger,
        string $folder,
        string $filename,
        string $ext,
    ): Response {
        $assets_path = __DIR__.'/../../assets';
        $path = "{$assets_path}/{$folder}/{$filename}.{$ext}";
        if (!is_file($path)) {
            throw new NotFoundHttpException('No such asset.');
        }
        return new BinaryFileResponse($path);
    }

    #[Route('/assets/user_initials_{initials}.svg', requirements: ['initials' => '.{0,3}'])]
    public function userInitials(
        Request $request,
        LoggerInterface $logger,
        string $initials,
    ): Response {
        $assets_path = __DIR__.'/../../assets';
        $svg_content = file_get_contents("{$assets_path}/icns/user_initials.svg");
        $out = str_replace('%INITIALS%', $initials, $svg_content);
        $response = new Response($out);
        $response->headers->set('Content-Type', 'image/svg+xml');
        return $response;
    }

    #[Route('/favicon.ico')]
    public function favicon(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $assets_path = __DIR__.'/../../assets';
        return new BinaryFileResponse("{$assets_path}/favicon.ico");
    }
}
