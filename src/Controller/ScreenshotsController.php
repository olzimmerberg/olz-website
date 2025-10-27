<?php

namespace Olz\Controller;

use Olz\Utils\EnvUtils;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScreenshotsController extends AbstractController {
    #[Route('/screenshots')]
    public function screenshots(
        Request $request,
        LoggerInterface $logger,
        EnvUtils $envUtils,
    ): Response {
        $code_href = $envUtils->getCodeHref();
        $main_href = 'https://olzimmerberg.ch/';

        $out = <<<'ZZZZZZZZZZ'
            <style>
            body { margin: 0; }
            #root .pair { border: 10px solid black; }
            #root.main { background-color: red; }
            #root.local { background-color: green; }
            #root.main .pair { border-color: red; }
            #root.local .pair { border-color: green; }
            #root .main { float: left; }
            #root .local { float: left; }
            #root.main .local { margin-left:-10000px; }
            #root.local .main { margin-left:-10000px; }
            #root .after-pair { clear: both; }
            </style>
            <script>
            let mode = 'local';
            window.setInterval(() => {
                mode = (mode === 'local' ? 'main' : 'local');
                document.getElementById('root').className = mode;
            }, 1000);
            </script>
            ZZZZZZZZZZ;

        $screenshot_paths = [];

        $generated_dir = "{$envUtils->getCodePath()}/screenshots/generated";
        $generated_contents = scandir($generated_dir);
        foreach ($generated_contents as $screenshot_path) {
            if ($screenshot_path[0] != '.') {
                $screenshot_paths[] = $screenshot_path;
            }
        }

        $main_index = json_decode(
            @file_get_contents("{$main_href}screenshots/index.json.php") ?: '',
            true
        );
        if ($main_index === null) {
            $out .= '<div>No JSON screenshot index on main</div>';
        } elseif (!isset($main_index['screenshot_paths'])) {
            $out .= '<div>Invalid JSON screenshot index on main</div>';
        } else {
            $main_paths = $main_index['screenshot_paths'];
            foreach ($main_paths as $main_path) {
                if (array_search($main_path, $screenshot_paths) === false) {
                    $screenshot_paths[] = $main_path;
                }
            }
        }

        sort($screenshot_paths);

        $out .= <<<'ZZZZZZZZZZ'
            <div id='root' class='local'>
            ZZZZZZZZZZ;
        foreach ($screenshot_paths as $screenshot_path) {
            $has_screenshot_id = preg_match('/^([a-z0-9\-\_]+)\.png$/', $screenshot_path, $matches);
            $screenshot_id = $has_screenshot_id ? " id='{$matches[1]}'" : "";
            $enc_screenshot_path = json_encode($screenshot_path);
            $out .= <<<ZZZZZZZZZZ
                <div class='pair'>
                <h2{$screenshot_id}>
                    <input type='checkbox' onchange='load(this, {$enc_screenshot_path})'/>
                    {$screenshot_path}
                </h2>
                <img class='local' id='local-{$screenshot_path}' />
                <img class='main' id='main-{$screenshot_path}' />
                <div class='after-pair'></div>
                </div>
                ZZZZZZZZZZ;
        }
        $out .= <<<ZZZZZZZZZZ
            </div>
            <script>
            function load(elem, screenshotPath) {
                const localElem = document.getElementById('local-'+screenshotPath);
                const mainElem = document.getElementById('main-'+screenshotPath);
                if (elem.checked) {
                    localElem.src = '{$code_href}screenshots/generated/' + screenshotPath;
                    mainElem.src = '{$main_href}screenshots/generated/' + screenshotPath;
                    localElem.style.display = 'block';
                    mainElem.style.display = 'block';
                } else {
                    localElem.style.display = 'none';
                    mainElem.style.display = 'none';
                }
            }
            </script>
            ZZZZZZZZZZ;

        return new Response($out);
    }

    #[Route('/screenshots.json')]
    public function screenshotsJson(
        Request $request,
        LoggerInterface $logger,
        EnvUtils $envUtils,
    ): Response {
        $generated_dir = "{$envUtils->getCodePath()}screenshots/generated";
        $generated_contents = scandir($generated_dir);
        $screenshot_paths = [];
        foreach ($generated_contents as $screenshot_path) {
            if ($screenshot_path[0] != '.') {
                $screenshot_paths[] = $screenshot_path;
            }
        }
        return new Response(json_encode(['screenshot_paths' => $screenshot_paths]) ?: '');
    }

    #[Route('/screenshots/generated/{name}.png')]
    public function screenshot(
        Request $request,
        LoggerInterface $logger,
        EnvUtils $envUtils,
        string $name,
    ): Response {
        $path = "{$envUtils->getCodePath()}screenshots/generated/{$name}.png";
        return new BinaryFileResponse($path);
    }
}
