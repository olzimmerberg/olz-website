<?php

namespace Olz\Service\Components\OlzDownloads;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Olz\Components\Common\OlzComponent;
use Olz\Entity\Service\Download;

/** @extends OlzComponent<array<string, mixed>> */
class OlzDownloads extends OlzComponent {
    public function getHtml(mixed $args): string {
        $code_href = $this->envUtils()->getCodeHref();
        $has_permission = $this->authUtils()->hasPermission('downloads');

        $out = '';
        $out .= "<h2>Downloads</h2>";

        if ($has_permission) {
            $out .= <<<ZZZZZZZZZZ
                <button
                    id='create-download-button'
                    class='btn btn-secondary'
                    onclick='return olz.initOlzEditDownloadModal()'
                >
                    <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                    Neuer Download
                </button>
                ZZZZZZZZZZ;
        }

        $download_repo = $this->entityManager()->getRepository(Download::class);
        $criteria = Criteria::create()->where(Criteria::expr()->andX(
            Criteria::expr()->eq('on_off', 1)
        ));
        $criteria = $criteria
            ->orderBy(['position' => Order::Ascending])
            ->setFirstResult(0)
            ->setMaxResults(100)
        ;
        $downloads = $download_repo->matching($criteria);

        $out .= "<ul class='no-style olz-downloads'>";
        foreach ($downloads as $download) {
            $id = $download->getId();
            $name = $download->getName();
            $on_off = $download->getOnOff();
            $file_id = $download->getFileId();

            $user = $this->authUtils()->getCurrentUser();
            $owner_user = $download->getOwnerUser();
            $is_owner = $user && $owner_user && intval($owner_user->getId() ?? 0) === intval($user->getId());
            $can_edit = $is_owner || $has_permission;
            $edit_admin = '';
            if ($can_edit) {
                $json_id = json_encode($id);
                $edit_admin = <<<ZZZZZZZZZZ
                    <button
                        id='edit-download-{$id}-button'
                        class='btn btn-secondary-outline btn-sm edit-download-list-button'
                        onclick='return olz.olzDownloadsEditDownload({$json_id})'
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
                        {$this->fileUtils()->olzFile('downloads', $id, $file_id, $name, $name)}
                    </li>
                    ZZZZZZZZZZ;
            }
        }
        $out .= "</ul>";

        return $out;
    }
}
