<?php

// =============================================================================
// Zeigt geplante und vergangene Termine an.
// =============================================================================

namespace Olz\Termine\Components\OlzTermineDetail;

use Doctrine\Common\Collections\Criteria;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Termine\Termin;
use Olz\Termine\Components\OlzTerminDetail\OlzTerminDetail;
use Olz\Termine\Utils\TermineFilterUtils;
use Olz\Utils\HttpUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzTermineDetail extends OlzComponent {
    public function getHtml($args = []): string {
        global $db_table, $id;

        require_once __DIR__.'/../../../../_/config/date.php';
        require_once __DIR__.'/../../../../_/config/paths.php';

        $code_href = $this->envUtils()->getCodeHref();
        $db = $this->dbUtils()->getDb();
        $entityManager = $this->dbUtils()->getEntityManager();
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($this->log());
        $validated_get_params = $http_utils->validateGetParams([
            'filter' => new FieldTypes\StringField(['allow_null' => true]),
            'id' => new FieldTypes\IntegerField(['allow_null' => true]),
            'buttontermine' => new FieldTypes\StringField(['allow_null' => true]),
        ], $_GET);

        $termine_utils = TermineFilterUtils::fromEnv();
        $termin_repo = $entityManager->getRepository(Termin::class);
        $is_not_archived = $termine_utils->getIsNotArchivedCriteria();
        $criteria = Criteria::create()
            ->where(Criteria::expr()->andX(
                $is_not_archived,
                Criteria::expr()->eq('id', $id),
            ))
            ->setFirstResult(0)
            ->setMaxResults(1)
        ;
        $news_entries = $termin_repo->matching($criteria);
        $num_news_entries = $news_entries->count();
        $no_robots = $num_news_entries !== 1;
        $host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
        $canonical_url = "https://{$host}{$code_href}termine.php?id={$id}";

        $out = '';

        $button_name = 'button'.$db_table;
        if (isset($_GET[$button_name])) {
            $_POST[$button_name] = $_GET[$button_name];
        }
        if (isset($_POST[$button_name])) {
            $_SESSION['edit']['db_table'] = $db_table;
        }

        $zugriff = ((($_SESSION['auth'] ?? null) == 'all') or in_array($db_table, preg_split('/ /', $_SESSION['auth'] ?? ''))) ? '1' : '0';

        $sql = "SELECT * FROM {$db_table} WHERE (id = '{$id}') ORDER BY datum DESC";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();

        $title = $row['titel'] ?? '';
        $back_filter = urlencode($_GET['filter'] ?? '{}');
        $out .= OlzHeader::render([
            'back_link' => "{$code_href}termine.php?filter={$back_filter}",
            'title' => "{$title} - Termine",
            'description' => "Orientierungslauf-Wettkämpfe, OL-Wochen, OL-Weekends, Trainings und Vereinsanlässe der OL Zimmerberg.",
            'norobots' => $no_robots,
            'canonical_url' => $canonical_url,
        ]);

        $id_edit = $_SESSION['id_edit'] ?? ''; // TODO: Entfernen?
        $out .= <<<ZZZZZZZZZZ
        <div class='content-right optional'>
            <div style='padding:4px 3px 10px 3px;'>
            </div>
        </div>
        <div class='content-middle'>
        <form name='Formularl' method='post' action='termine.php?id={$id}' enctype='multipart/form-data'>
        ZZZZZZZZZZ;

        // -------------------------------------------------------------
        // DATENSATZ EDITIEREN
        if ($zugriff) {
            $functions = ['neu' => 'Neuer Eintrag',
                'edit' => 'Bearbeiten',
                'abbruch' => 'Abbrechen',
                'vorschau' => 'Vorschau',
                'save' => 'Speichern',
                'delete' => 'Löschen',
                'start' => 'start',
                'duplicate' => 'duplicate',
                'undo' => 'undo', ];
        } else {
            $functions = [];
        }
        $function = array_search($_POST[$button_name] ?? null, $functions);
        if ($function != "") {
            ob_start();
            include __DIR__.'/../../../../_/admin/admin_db.php';
            $out .= ob_get_contents();
            ob_end_clean();
        }
        if (($_SESSION['edit']['table'] ?? null) == $db_table) {
            $db_edit = "1";
        } else {
            $db_edit = "0";
        }

        // -------------------------------------------------------------
        // AKTUELL - VORSCHAU
        if (($db_edit == "0") or (($do ?? null) == 'vorschau')) {
            $out .= OlzTerminDetail::render([
                'id' => $id,
                'can_edit' => $zugriff,
                'is_preview' => (($do ?? null) == 'vorschau'),
            ]);
        }

        $out .= "</form>
        </div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
