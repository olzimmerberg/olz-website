<?php

namespace Olz\Faq\Components\OlzFaqList;

use Olz\Components\Common\OlzPostingListItem\OlzPostingListItem;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Faq\Question;
use Olz\Entity\Faq\QuestionCategory;
use Olz\Entity\Roles\Role;
use Olz\Repository\Roles\PredefinedRole;
use Olz\Users\Components\OlzUserInfoModal\OlzUserInfoModal;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{von?: ?string}> */
class OlzFaqListParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzFaqList extends OlzRootComponent {
    public function getSearchTitle(): string {
        return 'TODO';
    }

    public function getSearchResults(array $terms): array {
        return [];
    }

    public static string $title = "Fragen & Antworten";
    public static string $description = "Antworten auf die wichtigsten Fragen rund um den OL, die OL Zimmerberg und diese Website.";

    public function getHtml(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzFaqListParams::class);
        $code_href = $this->envUtils()->getCodeHref();
        $entityManager = $this->dbUtils()->getEntityManager();
        $question_repo = $entityManager->getRepository(Question::class);
        $category_repo = $entityManager->getRepository(QuestionCategory::class);

        $out = OlzHeader::render([
            'title' => self::$title,
            'description' => self::$description,
            'canonical_url' => "{$code_href}fragen_und_antworten",
        ]);

        $role_repo = $entityManager->getRepository(Role::class);
        $nachwuchs_role = $role_repo->getPredefinedRole(PredefinedRole::Nachwuchs);
        $nachwuchs_out = '';
        $nachwuchs_assignees = $nachwuchs_role?->getUsers() ?? [];
        foreach ($nachwuchs_assignees as $nachwuchs_assignee) {
            $nachwuchs_out .= OlzUserInfoModal::render([
                'user' => $nachwuchs_assignee,
                'mode' => 'name_picture',
            ]);
        }

        $out .= <<<ZZZZZZZZZZ
            <div class='content-right'>
                <h3>Ansprechperson</h3>
                <div style='padding:0px 10px 0px 10px; text-align:center;'>
                    {$nachwuchs_out}
                </div>
            </div>
            <div class='content-middle olz-faq-list'>
                <h1>Fragen & Antworten (FAQ)</h1>
            ZZZZZZZZZZ;

        $has_access = $this->authUtils()->hasPermission('faq');
        if ($has_access) {
            $out .= <<<ZZZZZZZZZZ
                <button
                    id='create-question-category-button'
                    class='btn btn-secondary'
                    onclick='return olz.initOlzEditQuestionCategoryModal()'
                >
                    <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                    Neue Frage-Kategorie
                </button>
                ZZZZZZZZZZ;
        }

        $categories = $category_repo->findBy(['on_off' => 1], ['position' => 'ASC']);
        foreach ($categories as $category) {
            $create_admin = '';
            $edit_admin = '';
            if ($has_access) {
                $json_id = json_encode($category->getId());
                $create_admin = <<<ZZZZZZZZZZ
                    <button
                        id='create-question-button'
                        class='btn btn-secondary'
                        onclick='return olz.initOlzEditQuestionModal(undefined, undefined, {categoryId: {$json_id}})'
                    >
                        <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                        Neue Frage
                    </button>
                    ZZZZZZZZZZ;
                $edit_admin = <<<ZZZZZZZZZZ
                    <button
                        class='btn btn-secondary-outline btn-sm edit-question-category-list-button'
                        onclick='return olz.faqListEditQuestionCategory({$json_id})'
                    >
                        <img src='{$code_href}assets/icns/edit_16.svg' class='noborder' />
                    </button>
                    ZZZZZZZZZZ;
            }

            $out .= "<h2 class='category'>{$category->getName()}{$edit_admin}</h2>";
            $out .= $create_admin;
            $questions = $question_repo->findBy(
                ['category' => $category, 'on_off' => 1],
                ['position_within_category' => 'ASC'],
            );
            foreach ($questions as $question) {
                $icon = "{$code_href}assets/icns/question_mark_20.svg";
                $link = "fragen_und_antworten/{$question->getIdent()}";
                $edit_admin = '';
                if ($has_access) {
                    $json_id = json_encode($question->getId());
                    $edit_admin = <<<ZZZZZZZZZZ
                        <button
                            class='btn btn-secondary-outline btn-sm edit-question-list-button'
                            onclick='return olz.faqListEditQuestion({$json_id})'
                        >
                            <img src='{$code_href}assets/icns/edit_16.svg' class='noborder' />
                        </button>
                        ZZZZZZZZZZ;
                }

                $out .= OlzPostingListItem::render([
                    'icon' => $icon,
                    'title' => $question->getQuestion().$edit_admin,
                    'link' => $link,
                ]);
            }
        }

        $out .= "</div>";

        $out .= OlzFooter::render();
        return $out;
    }
}
