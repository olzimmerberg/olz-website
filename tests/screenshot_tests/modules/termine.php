<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$termine_url = '/termine';
$termine_id_7_url = "{$termine_url}/7";
$termine_id_1002_url = "{$termine_url}/1002";

function test_termine($driver, $base_url) {
    global $termine_url, $termine_id_7_url, $termine_id_1002_url;
    tick('termine');

    test_termine_readonly($driver, $base_url);

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$termine_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$termine_url}");

    $create_termin_button = $driver->findElement(
        WebDriverBy::cssSelector('#create-termin-button')
    );
    click($create_termin_button);
    sleep(1);
    $start_time_input = $driver->findElement(
        WebDriverBy::cssSelector('#termin-start-time-input')
    );
    sendKeys($start_time_input, '14:00');
    $end_date_input = $driver->findElement(
        WebDriverBy::cssSelector('#termin-end-date-input')
    );
    sendKeys($end_date_input, '2020-08-15');
    $end_time_input = $driver->findElement(
        WebDriverBy::cssSelector('#termin-end-time-input')
    );
    sendKeys($end_time_input, '18:00');
    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#termin-title-input')
    );
    sendKeys($title_input, 'Der Event');
    $text_input = $driver->findElement(
        WebDriverBy::cssSelector('#termin-text-input')
    );
    sendKeys($text_input, '...wird episch!');
    $link_input = $driver->findElement(
        WebDriverBy::cssSelector('#termin-link-input')
    );
    sendKeys($link_input, '<a href="https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id=6822" class="linkext">Infos</a>');
    $deadline_input = $driver->findElement(
        WebDriverBy::cssSelector('#termin-deadline-input')
    );
    sendKeys($deadline_input, '2020-08-01 23:59:59');
    $has_newsletter_input = $driver->findElement(
        WebDriverBy::cssSelector('#termin-has-newsletter-input')
    );
    click($has_newsletter_input);
    $solv_id_input = $driver->findElement(
        WebDriverBy::cssSelector('#termin-solv-id-input')
    );
    sendKeys($solv_id_input, '6822');
    $go2ol_id_input = $driver->findElement(
        WebDriverBy::cssSelector('#termin-go2ol-id-input')
    );
    sendKeys($go2ol_id_input, '2014nat6');
    $types_programm_input = $driver->findElement(
        WebDriverBy::cssSelector('#termin-types-programm-input')
    );
    click($types_programm_input);
    $types_ol_input = $driver->findElement(
        WebDriverBy::cssSelector('#termin-types-ol-input')
    );
    click($types_ol_input);
    $coordinate_x_input = $driver->findElement(
        WebDriverBy::cssSelector('#termin-coordinate-x-input')
    );
    sendKeys($coordinate_x_input, '735550');
    $coordinate_y_input = $driver->findElement(
        WebDriverBy::cssSelector('#termin-coordinate-y-input')
    );
    sendKeys($coordinate_y_input, '188600');

    $image_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#termin-images-upload input[type=file]')
    );
    $image_path = realpath(__DIR__.'/../../../assets/icns/schilf.jpg');
    sendKeys($image_upload_input, $image_path);
    $driver->wait()->until(function () use ($driver) {
        $image_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#termin-images-upload .olz-upload-image.uploaded')
        );
        return count($image_uploaded) == 1;
    });

    $file_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#termin-files-upload input[type=file]')
    );
    $document_path = realpath(__DIR__.'/../../../src/Utils/data/sample-data/sample-document.pdf');
    sendKeys($file_upload_input, $document_path);
    $driver->wait()->until(function () use ($driver) {
        $file_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#termin-files-upload .olz-upload-file.uploaded')
        );
        return count($file_uploaded) == 1;
    });

    take_pageshot($driver, 'termine_new_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#submit-button')
    );
    click($save_button);
    sleep(4);
    $driver->get("{$base_url}{$termine_id_1002_url}");
    take_pageshot($driver, 'termine_new_finished');

    $driver->get("{$base_url}{$termine_id_7_url}");
    take_pageshot($driver, 'termine_detail');

    logout($driver, $base_url);

    reset_dev_data();
    tock('termine', 'termine');
}

function test_termine_readonly($driver, $base_url) {
    global $termine_url;
    $driver->get("{$base_url}{$termine_url}");
    take_pageshot($driver, 'termine');

    $filter_type_training = $driver->findElement(
        WebDriverBy::cssSelector('#filter-type-training')
    );
    click($filter_type_training);
    $filter_date_2020 = $driver->findElement(
        WebDriverBy::cssSelector('#filter-date-2020')
    );
    $filter_date_2020->click();

    take_pageshot($driver, 'termine_past');
}
