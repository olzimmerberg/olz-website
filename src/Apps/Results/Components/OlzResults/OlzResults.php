<?php

namespace Olz\Apps\Results\Components\OlzResults;

use Olz\Apps\Results\Metadata;
use Olz\Components\Apps\OlzNoAppAccess\OlzNoAppAccess;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{file?: ?string}> */
class OlzResultsParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzResults extends OlzRootComponent {
    public function getSearchTitle(): string {
        return 'TODO';
    }

    public function getSearchResults(array $terms): array {
        return [];
    }

    public function getHtml(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzResultsParams::class);

        $code_href = $this->envUtils()->getCodeHref();
        $data_path = $this->envUtils()->getDataPath();
        $filename = $this->getParams()['file'] ?? null;
        $metadata = new Metadata();

        $out = OlzHeader::render([
            'back_link' => "{$code_href}service/",
            'title' => "Resultate",
            'norobots' => true,
        ]);

        $out .= "<div class='content-full'>";
        if ($filename !== null) {
            if (is_file("{$data_path}results/{$filename}")) {
                $edit_admin = '';
                if ($this->authUtils()->hasPermission('any')) {
                    $enc_data = json_encode(['file' => $filename]);
                    $edit_admin = <<<ZZZZZZZZZZ
                        <button
                            id='live-results-button'
                            class='btn btn-secondary'
                            onclick='return olzResults.initOlzLiveResultsModal({$enc_data})'
                        >
                            <img src='{$code_href}assets/icns/edit_white_16.svg' class='noborder' />
                            Live Resultate überschreiben
                        </button>
                        <button
                            id='edit-result-button'
                            class='btn btn-secondary'
                            onclick='return olzResults.initOlzEditResultModal({$enc_data})'
                        >
                            <img src='{$code_href}assets/icns/edit_white_16.svg' class='noborder' />
                            Resultate bearbeiten
                        </button>
                        ZZZZZZZZZZ;
                }
                $out .= <<<ZZZZZZZZZZ
                    {$edit_admin}
                    <div id='title-box'>
                        <div id='backbutton' onclick='olzResults.popHash()'>&lt;</div>
                        <h1 id='title'></h1>
                    </div>
                    <div id='results-content'>
                        <div id='classes-box'></div>
                        <div id='content-box'></div>
                        <div class='inactive' id='grafik-box'><svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' style='width:100%; height:100%;' id='grafik-svg'></svg></div>
                    </div>
                    ZZZZZZZZZZ;
            } elseif ($this->authUtils()->hasPermission('any')) {
                $enc_data = json_encode(['file' => $filename]);
                $out .= <<<ZZZZZZZZZZ
                    <div>
                        <button
                            id='live-results-button'
                            class='btn btn-secondary'
                            onclick='return olzResults.initOlzLiveResultsModal({$enc_data})'
                        >
                            <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                            Live Resultate hochladen
                        </button>
                        <button
                            id='create-result-button'
                            class='btn btn-secondary'
                            onclick='return olzResults.initOlzEditResultModal({$enc_data})'
                        >
                            <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                            Resultate hochladen
                        </button>
                    </div>
                    ZZZZZZZZZZ;
            } else {
                $out .= OlzNoAppAccess::render([
                    'app' => $metadata,
                ]);
            }
        } else {
            $out .= "<ul>";
            $contents = scandir("{$data_path}results") ?: [];
            foreach ($contents as $entry) {
                if (preg_match('/\.xml$/', $entry) && !preg_match('/\.bak\./', $entry)) {
                    $out .= "<li><a href='?file={$entry}'>{$entry}</a></li>\n";
                }
            }
            $out .= "</ul>";
            if ($this->authUtils()->hasPermission('any')) {
                $out .= <<<ZZZZZZZZZZ
                    <div>
                        <button
                            id='live-results-button'
                            class='btn btn-secondary'
                            onclick='return olzResults.initOlzLiveResultsModal()'
                        >
                            <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                            Live Resultate hochladen
                        </button>
                        <button
                            id='create-result-button'
                            class='btn btn-secondary'
                            onclick='return olzResults.initOlzEditResultModal()'
                        >
                            <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                            Resultate hochladen
                        </button>
                    </div>
                    ZZZZZZZZZZ;
            }
        }
        $out .= "</div>";

        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();

        return $out;
    }
}
