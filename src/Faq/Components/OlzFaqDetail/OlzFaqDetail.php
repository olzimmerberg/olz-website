<?php

namespace Olz\Faq\Components\OlzFaqDetail;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Faq\Question;
use Olz\Entity\Roles\Role;
use Olz\Repository\Roles\PredefinedRole;
use Olz\Users\Components\OlzUserInfoModal\OlzUserInfoModal;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzFaqDetailParams extends HttpParams {
}

/** @extends OlzComponent<array<string, mixed>> */
class OlzFaqDetail extends OlzComponent {
    public function getHtml(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzFaqDetailParams::class);
        $code_href = $this->envUtils()->getCodeHref();
        $entityManager = $this->dbUtils()->getEntityManager();
        $ident = $args['ident'] ?? null;

        $question_repo = $entityManager->getRepository(Question::class);
        $answered_question = $question_repo->findOneBy(['ident' => $ident]);
        if (!$answered_question) {
            $this->httpUtils()->dieWithHttpError(404);
        }
        $is_active = $answered_question->getOnOff();
        if (!$is_active && !$this->authUtils()->hasPermission('faq')) {
            $this->httpUtils()->dieWithHttpError(404);
        }

        $question = $answered_question->getQuestion();
        $out = OlzHeader::render([
            'back_link' => "{$code_href}fragen_und_antworten",
            'title' => "{$question} - Fragen & Antworten",
            'description' => "Antworten auf die wichtigsten Fragen rund um den OL, die OL Zimmerberg und diese Website.",
        ]);

        $answer = $answered_question->getAnswer();
        $answer_html = $this->htmlUtils()->renderMarkdown($answer);

        $role_repo = $entityManager->getRepository(Role::class);
        $nachwuchs_role = $role_repo->getPredefinedRole(PredefinedRole::Nachwuchs);
        $nachwuchs_out = '';
        $nachwuchs_assignees = $nachwuchs_role->getUsers();
        foreach ($nachwuchs_assignees as $nachwuchs_assignee) {
            $nachwuchs_out .= OlzUserInfoModal::render([
                'user' => $nachwuchs_assignee,
                'mode' => 'name_picture',
            ]);
        }

        $out .= <<<ZZZZZZZZZZ
            <div class='content-right optional'>
                <h3>Ansprechperson</h3>
                <div style='padding:0px 10px 0px 10px; text-align:center;'>
                    {$nachwuchs_out}
                </div>
            </div>
            <div class='content-middle'>
                <h1>{$question}</h1>
                <div>{$answer_html}</div>
            </div>
            ZZZZZZZZZZ;

        $out .= OlzFooter::render();
        return $out;
    }
}
