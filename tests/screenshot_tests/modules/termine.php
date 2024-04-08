<?php

namespace Facebook\WebDriver;

require_once __DIR__.'/../utils/database.php';
require_once __DIR__.'/../utils/screenshot.php';
require_once __DIR__.'/../utils/wrappers.php';

$termine_url = '/termine';
$termine_id_7_url = "{$termine_url}/7";
$termine_id_1002_url = "{$termine_url}/1002";

$termin_locations_url = '/termine/orte';
$termin_location_id_3_url = "{$termin_locations_url}/3";
$termin_location_id_4_url = "{$termin_locations_url}/4";

$termin_templates_url = '/termine/vorlagen';
$termin_template_id_2_url = "{$termin_templates_url}/2";
$termin_template_id_7_url = "{$termin_templates_url}/7";

function test_termine($driver, $base_url) {
    tick('termine');

    test_termine_readonly($driver, $base_url);
    test_create_termin_new($driver, $base_url);
    test_create_termin_location_new($driver, $base_url);
    test_create_termin_template_new($driver, $base_url);

    reset_dev_data();
    tock('termine', 'termine');
}

function test_create_termin_new($driver, $base_url) {
    global $termine_url, $termine_id_7_url, $termine_id_1002_url;

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
        WebDriverBy::cssSelector('#startTime-input')
    );
    sendKeys($start_time_input, '14:00');
    $end_date_input = $driver->findElement(
        WebDriverBy::cssSelector('#endDate-input')
    );
    sendKeys($end_date_input, '2020-08-15');
    $end_time_input = $driver->findElement(
        WebDriverBy::cssSelector('#endTime-input')
    );
    sendKeys($end_time_input, '18:00');
    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#title-input')
    );
    sendKeys($title_input, 'Der Event');
    $text_input = $driver->findElement(
        WebDriverBy::cssSelector('#text-input')
    );
    sendKeys($text_input, '...wird episch!');
    $link_input = $driver->findElement(
        WebDriverBy::cssSelector('#link-input')
    );
    sendKeys($link_input, '<a href="https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id=6822" class="linkext">Infos</a>');
    $deadline_input = $driver->findElement(
        WebDriverBy::cssSelector('#deadline-input')
    );
    sendKeys($deadline_input, '2020-08-01 23:59:59');
    $types_programm_input = $driver->findElement(
        WebDriverBy::cssSelector('#types-programm-input')
    );
    click($types_programm_input);
    $types_ol_input = $driver->findElement(
        WebDriverBy::cssSelector('#types-ol-input')
    );
    click($types_ol_input);
    $coordinate_x_input = $driver->findElement(
        WebDriverBy::cssSelector('#coordinateX-input')
    );
    sendKeys($coordinate_x_input, '735550');
    $coordinate_y_input = $driver->findElement(
        WebDriverBy::cssSelector('#coordinateY-input')
    );
    sendKeys($coordinate_y_input, '188600');

    $image_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#images-upload input[type=file]')
    );
    $image_path = realpath(__DIR__.'/../../../assets/icns/schilf.jpg');
    sendKeys($image_upload_input, $image_path);
    $driver->wait()->until(function () use ($driver) {
        $image_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#images-upload .olz-upload-image.uploaded')
        );
        return count($image_uploaded) == 1;
    });

    $file_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#files-upload input[type=file]')
    );
    $document_path = realpath(__DIR__.'/../../../src/Utils/data/sample-data/sample-document.pdf');
    sendKeys($file_upload_input, $document_path);
    $driver->wait()->until(function () use ($driver) {
        $file_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#files-upload .olz-upload-file.uploaded')
        );
        return count($file_uploaded) == 1;
    });

    $has_newsletter_input = $driver->findElement(
        WebDriverBy::cssSelector('#hasNewsletter-input')
    );
    click($has_newsletter_input);

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
}

function test_create_termin_location_new($driver, $base_url) {
    global $termin_locations_url, $termin_location_id_3_url, $termin_location_id_4_url;

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$termin_locations_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$termin_locations_url}");

    $create_termin_location_button = $driver->findElement(
        WebDriverBy::cssSelector('#create-termin-location-button')
    );
    click($create_termin_location_button);
    sleep(1);
    $name_input = $driver->findElement(
        WebDriverBy::cssSelector('#name-input')
    );
    sendKeys($name_input, 'Der Austragungsort');
    $details_input = $driver->findElement(
        WebDriverBy::cssSelector('#details-input')
    );
    sendKeys($details_input, '...ist perfekt!');
    $latitude_input = $driver->findElement(
        WebDriverBy::cssSelector('#latitude-input')
    );
    sendKeys($latitude_input, '46.83479');
    $longitude_input = $driver->findElement(
        WebDriverBy::cssSelector('#longitude-input')
    );
    sendKeys($longitude_input, '9.21555');

    $image_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#images-upload input[type=file]')
    );
    $image_path = realpath(__DIR__.'/../../../assets/icns/schilf.jpg');
    sendKeys($image_upload_input, $image_path);
    $driver->wait()->until(function () use ($driver) {
        $image_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#images-upload .olz-upload-image.uploaded')
        );
        return count($image_uploaded) == 1;
    });

    take_pageshot($driver, 'termin_locations_new_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#submit-button')
    );
    click($save_button);
    sleep(4);
    $driver->get("{$base_url}{$termin_location_id_4_url}");
    take_pageshot($driver, 'termin_locations_new_finished');

    $driver->get("{$base_url}{$termin_location_id_3_url}");
    take_pageshot($driver, 'termin_locations_detail');

    logout($driver, $base_url);
}

function test_create_termin_template_new($driver, $base_url) {
    global $termin_templates_url, $termin_template_id_2_url, $termin_template_id_7_url;

    login($driver, $base_url, 'admin', 'adm1n');
    $driver->get("{$base_url}{$termin_templates_url}");
    $driver->navigate()->refresh();
    $driver->get("{$base_url}{$termin_templates_url}");

    $create_termin_template_button = $driver->findElement(
        WebDriverBy::cssSelector('#create-termin-template-button')
    );
    click($create_termin_template_button);
    sleep(1);
    $start_time_input = $driver->findElement(
        WebDriverBy::cssSelector('#startTime-input')
    );
    sendKeys($start_time_input, '14:00');
    $duration_seconds_input = $driver->findElement(
        WebDriverBy::cssSelector('#durationSeconds-input')
    );
    sendKeys($duration_seconds_input, '7200');
    $title_input = $driver->findElement(
        WebDriverBy::cssSelector('#title-input')
    );
    sendKeys($title_input, 'Die Event-Vorlage');
    $text_input = $driver->findElement(
        WebDriverBy::cssSelector('#text-input')
    );
    sendKeys($text_input, '...wird jedes Mal episch!');
    $link_input = $driver->findElement(
        WebDriverBy::cssSelector('#link-input')
    );
    sendKeys($link_input, '<a href="https://www.o-l.ch/cgi-bin/fixtures?&mode=show&unique_id=6822" class="linkext">immer dasselbe</a>');
    $deadline_earlier_seconds_input = $driver->findElement(
        WebDriverBy::cssSelector('#deadlineEarlierSeconds-input')
    );
    sendKeys($deadline_earlier_seconds_input, '259200');
    $deadline_time_input = $driver->findElement(
        WebDriverBy::cssSelector('#deadlineTime-input')
    );
    sendKeys($deadline_time_input, '23:59:59');
    $types_programm_input = $driver->findElement(
        WebDriverBy::cssSelector('#types-programm-input')
    );
    click($types_programm_input);
    $types_ol_input = $driver->findElement(
        WebDriverBy::cssSelector('#types-ol-input')
    );
    click($types_ol_input);
    $location_dropdown_button = $driver->findElement(
        WebDriverBy::cssSelector('#locationId-field button[data-bs-toggle="dropdown"]')
    );
    click($location_dropdown_button);
    $location_item_button = $driver->findElement(
        WebDriverBy::cssSelector('#locationId-field #entity-index-1')
    );
    click($location_item_button);

    $image_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#images-upload input[type=file]')
    );
    $image_path = realpath(__DIR__.'/../../../assets/icns/schilf.jpg');
    sendKeys($image_upload_input, $image_path);
    $driver->wait()->until(function () use ($driver) {
        $image_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#images-upload .olz-upload-image.uploaded')
        );
        return count($image_uploaded) == 1;
    });

    $file_upload_input = $driver->findElement(
        WebDriverBy::cssSelector('#files-upload input[type=file]')
    );
    $document_path = realpath(__DIR__.'/../../../src/Utils/data/sample-data/sample-document.pdf');
    sendKeys($file_upload_input, $document_path);
    $driver->wait()->until(function () use ($driver) {
        $file_uploaded = $driver->findElements(
            WebDriverBy::cssSelector('#files-upload .olz-upload-file.uploaded')
        );
        return count($file_uploaded) == 1;
    });

    $has_newsletter_input = $driver->findElement(
        WebDriverBy::cssSelector('#hasNewsletter-input')
    );
    click($has_newsletter_input);

    take_pageshot($driver, 'termin_templates_new_edit');

    $save_button = $driver->findElement(
        WebDriverBy::cssSelector('#submit-button')
    );
    click($save_button);
    sleep(4);
    $driver->get("{$base_url}{$termin_template_id_7_url}");
    take_pageshot($driver, 'termin_templates_new_finished');

    $driver->get("{$base_url}{$termin_template_id_2_url}");
    take_pageshot($driver, 'termin_templates_detail');

    logout($driver, $base_url);
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
