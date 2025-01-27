<?php

namespace Olz\Service\Components\OlzLinks;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Olz\Components\Common\OlzComponent;
use Olz\Entity\Service\Link;

class OlzLinks extends OlzComponent {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $has_permission = $this->authUtils()->hasPermission('links');
        $code_href = $this->envUtils()->getCodeHref();

        $out = '';

        $out .= "<h2>Links</h2>";

        if ($has_permission) {
            $out .= <<<ZZZZZZZZZZ
                <button
                    id='create-link-button'
                    class='btn btn-secondary'
                    onclick='return olz.initOlzEditLinkModal()'
                >
                    <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                    Neuer Link
                </button>
                ZZZZZZZZZZ;
        }

        $link_repo = $this->entityManager()->getRepository(Link::class);
        $criteria = Criteria::create()->where(Criteria::expr()->andX(
            Criteria::expr()->eq('on_off', 1)
        ));
        $criteria = $criteria
            ->orderBy(['position' => Order::Ascending])
            ->setFirstResult(0)
            ->setMaxResults(100)
        ;
        $links = $link_repo->matching($criteria);

        $out .= "<ul class='olz-links nobox'>";
        foreach ($links as $link) {
            $id = $link->getId();
            $name = $link->getName();
            $on_off = $link->getOnOff();
            $url = $link->getUrl();

            $user = $this->authUtils()->getCurrentUser();
            $owner_user = $link->getOwnerUser();
            $is_owner = $user && $owner_user && intval($owner_user->getId() ?? 0) === intval($user->getId());
            $can_edit = $is_owner || $has_permission;
            $edit_admin = '';
            if ($can_edit) {
                $json_id = json_encode(intval($id));
                $edit_admin = <<<ZZZZZZZZZZ
                    <button
                        id='edit-link-{$id}-button'
                        class='btn btn-secondary-outline btn-sm edit-link-list-button'
                        onclick='return olz.olzLinksEditLink({$json_id})'
                    >
                        <img src='{$code_href}assets/icns/edit_16.svg' class='noborder' />
                    </button>
                    ZZZZZZZZZZ;
            }

            $class = '';
            if ($on_off == 0) {
                $class .= " error";
            }
            if ($can_edit) {
                $class .= " edit-flex";
            }

            if ($name === '---') {
                $out .= "{$edit_admin}<br />";
            } else {
                $out .= <<<ZZZZZZZZZZ
                    <li class='{$class}'>
                        {$edit_admin}
                        <a href='{$url}' class='linkext' target='_blank'>{$name}</a>
                    </li>
                    ZZZZZZZZZZ;
            }
        }
        $out .= "</ul>";

        return $out;
    }
}
