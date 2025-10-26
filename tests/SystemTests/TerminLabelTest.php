<?php

declare(strict_types=1);

namespace Olz\Tests\SystemTests;

use Facebook\WebDriver\WebDriverBy;
use Olz\Tests\SystemTests\Common\OnlyInModes;
use Olz\Tests\SystemTests\Common\SystemTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class TerminLabelTest extends SystemTestCase {
    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminLabelUpdate(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->assertSame([
            'Alle Termine',
            'Jahresprogramm',
            'Weekends',
            'Trainings',
            'OLZ-Trophy',
            'Wettkämpfe',
            'Vereinsanlässe',
            'Meldeschlüsse',
        ], array_map(
            fn ($elem) => $elem->getText(),
            $this->getBrowserElements('.type-filter')
        ));

        $this->click('#edit-termin-label-button');
        $this->waitForModal('#edit-termin-label-modal');
        $this->sendKeys('#edit-termin-label-modal #ident-input', '_updated');
        $this->sendKeys('#edit-termin-label-modal #name-input', ' UPDATED');
        $this->sendKeys('#edit-termin-label-modal #details-input', ' ...Ergänzung.');

        $icon_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        assert($icon_path);
        $this->sendKeys('#edit-termin-label-modal #icon-upload input[type=file]', $icon_path);
        $browser->wait()->until(function () use ($browser) {
            $icon_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-termin-label-modal #icon-upload .olz-upload-image.uploaded')
            );
            return count($icon_uploaded) == 1;
        });

        $this->selectOption('#edit-termin-label-modal #position-field #before-after-input', 'vor');
        $this->selectOption('#edit-termin-label-modal #position-field .olz-entity-chooser', 'Vereinsanlässe');

        $image_path = realpath(__DIR__.'/../../assets/icns/schilf.jpg');
        assert($image_path);
        $this->sendKeys('#edit-termin-label-modal #images-upload input[type=file]', $image_path);
        $browser->wait()->until(function () use ($browser) {
            $image_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-termin-label-modal #images-upload .olz-upload-image.uploaded')
            );
            return count($image_uploaded) == 2;
        });

        $document_path = realpath(__DIR__.'/../../src/Utils/data/sample-data/sample-document.pdf');
        assert($document_path);
        $this->sendKeys('#edit-termin-label-modal #files-upload input[type=file]', $document_path);
        $browser->wait()->until(function () use ($browser) {
            $file_uploaded = $browser->findElements(
                WebDriverBy::cssSelector('#edit-termin-label-modal #files-upload .olz-upload-file.uploaded')
            );
            return count($file_uploaded) == 2;
        });

        $this->screenshot('termin_label_edit');
        $this->waitABit();

        $this->click('#edit-termin-label-modal #submit-button');
        $this->waitUntilGone('#edit-termin-label-modal');

        $browser->get($this->getUrl());
        $this->waitUntilGone('#redirect-link');
        $this->waitABit();
        $this->assertSame([
            'Alle Termine',
            'Jahresprogramm',
            'Weekends',
            'OLZ-Trophy',
            'Wettkämpfe',
            'Trainings UPDATED',
            'Vereinsanlässe',
            'Meldeschlüsse',
        ], array_map(
            fn ($elem) => $elem->getText(),
            $this->getBrowserElements('.type-filter')
        ));

        $this->assertSame(410, $this->getHeaders($this->getUrl())['http_code']);
        $this->assertSame(200, $this->getHeaders($this->getUpdatedUrl())['http_code']);

        $this->resetDb();
    }

    #[OnlyInModes(['dev_rw', 'staging_rw'])]
    public function testTerminLabelDelete(): void {
        $browser = $this->getBrowser();

        $this->login('admin', 'adm1n');
        $browser->get($this->getUrl());

        $this->click('#edit-termin-label-button');
        $this->waitForModal('#edit-termin-label-modal');
        $this->click('#edit-termin-label-modal #delete-button');
        $this->waitForModal('#confirmation-dialog-modal');
        $this->click('#confirmation-dialog-modal #confirm-button');
        $this->waitUntilGone('#confirmation-dialog-modal');
        $this->waitUntilGone('#edit-termin-label-modal');

        $this->assertSame(410, $this->getHeaders($this->getUrl())['http_code']);

        $this->resetDb();
    }

    protected function getUrl(): string {
        return "{$this->getTargetUrl()}/termine?filter=typ-training---datum-bevorstehend";
    }

    protected function getUpdatedUrl(): string {
        return "{$this->getTargetUrl()}/termine?filter=typ-training_updated---datum-bevorstehend";
    }
}
