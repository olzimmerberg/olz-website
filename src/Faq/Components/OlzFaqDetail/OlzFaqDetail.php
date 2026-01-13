<?php

namespace Olz\Faq\Components\OlzFaqDetail;

use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Faq\Question;
use Olz\Entity\Roles\Role;
use Olz\Repository\Roles\PredefinedRole;
use Olz\Roles\Components\OlzRoleInfoModal\OlzRoleInfoModal;
use Olz\Users\Components\OlzUserInfoModal\OlzUserInfoModal;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{von?: ?string}> */
class OlzFaqDetailParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzFaqDetail extends OlzRootComponent {
    public function hasAccess(): bool {
        return true;
    }

    public function getSearchTitle(): string {
        return 'Fragen & Antworten';
    }

    public function searchSqlWhenHasAccess(array $terms): ?string {
        $code_href = $this->envUtils()->getCodeHref();
        $where = implode(' AND ', array_map(function ($term) {
            return <<<ZZZZZZZZZZ
                (
                    ident LIKE '%{$term}%'
                    OR question LIKE '%{$term}%'
                    OR answer LIKE '%{$term}%'
                )
                ZZZZZZZZZZ;
        }, $terms));
        return <<<ZZZZZZZZZZ
            SELECT
                CONCAT('{$code_href}fragen_und_antworten/', ident) AS link,
                '{$code_href}assets/icns/question_mark_20.svg' AS icon,
                NULL AS date,
                question AS title,
                CONCAT(IFNULL(ident, ''), ' ', IFNULL(answer, '')) AS text,
                1.0 AS time_relevance
            FROM questions
            WHERE
                on_off = '1'
                AND {$where}
            ZZZZZZZZZZ;
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzFaqDetailParams::class);
        $code_href = $this->envUtils()->getCodeHref();
        $entityManager = $this->dbUtils()->getEntityManager();
        $ident = $args['ident'] ?? null;

        $question_repo = $entityManager->getRepository(Question::class);
        $answered_question = $question_repo->findOneBy(['ident' => $ident]);
        if (!$answered_question) {
            $this->httpUtils()->dieWithHttpError(404);
            throw new \Exception('should already have failed');
        }
        $is_active = $answered_question->getOnOff();
        if (!$is_active && !$this->authUtils()->hasPermission('faq')) {
            $this->httpUtils()->dieWithHttpError(404);
            throw new \Exception('should already have failed');
        }

        $question = $answered_question->getQuestion();
        $out = OlzHeader::render([
            'back_link' => "{$code_href}fragen_und_antworten",
            'title' => "{$question} - Fragen & Antworten",
            'description' => "Antworten auf die wichtigsten Fragen rund um den OL, die OL Zimmerberg und diese Website.",
            'canonical_url' => "{$code_href}fragen_und_antworten/{$ident}",
        ]);

        $answer = $answered_question->getAnswer() ?? '';
        $answer_html = $this->htmlUtils()->renderMarkdown($answer);
        $answer_html = $answered_question->replaceImagePaths($answer_html);
        $answer_html = $answered_question->replaceFilePaths($answer_html);

        $edit_admin = '';
        $can_edit = $this->authUtils()->hasPermission('faq');
        if ($can_edit) {
            $id = $answered_question->getId();
            $json_id = json_encode($id);
            $edit_admin = <<<ZZZZZZZZZZ
                <div>
                    <button
                        id='edit-question-button'
                        class='btn btn-primary'
                        onclick='return olz.editQuestion({$json_id})'
                    >
                        <img src='{$code_href}assets/icns/edit_white_16.svg' class='noborder' />
                        Bearbeiten
                    </button>
                </div>
                ZZZZZZZZZZ;
        }

        $owner_role = $answered_question->getOwnerRole();
        $role_repo = $entityManager->getRepository(Role::class);
        $responsible_role = $owner_role ?? $role_repo->getPredefinedRole(PredefinedRole::Nachwuchs);
        $responsible_title = 'Ansprechperson';
        if ($owner_role) {
            $responsible_title = OlzRoleInfoModal::render(['role' => $owner_role]);
        }
        $responsible_assignees = $responsible_role?->getUsers() ?? [];
        $responsible_out = '';
        foreach ($responsible_assignees as $responsible_assignee) {
            $responsible_out .= OlzUserInfoModal::render([
                'user' => $responsible_assignee,
                'mode' => 'name_picture',
            ]);
        }

        $out .= <<<ZZZZZZZZZZ
            <div class='content-right optional'>
                <h3>{$responsible_title}</h3>
                <div style='padding:0px 10px 0px 10px; text-align:center;'>
                    {$responsible_out}
                </div>
            </div>
            <div class='content-middle'>
                {$edit_admin}
                <h1>{$question}</h1>
                <div>{$answer_html}</div>
            </div>
            ZZZZZZZZZZ;

        $out .= OlzFooter::render();
        return $out;
    }
}
