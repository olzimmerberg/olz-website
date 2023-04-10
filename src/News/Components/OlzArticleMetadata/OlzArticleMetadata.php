<?php

namespace Olz\News\Components\OlzArticleMetadata;

use Olz\Components\Common\OlzComponent;

class OlzArticleMetadata extends OlzComponent {
    public function getHtml($args = []): string {
        global $data_path, $data_href, $base_href, $code_href;
        $db = $this->dbUtils()->getDb();

        $id = intval($args['id']);
        $sql = "SELECT autor, titel, datum, zeit FROM aktuell WHERE id='{$id}'";
        $res = $db->query($sql);
        if ($res->num_rows == 0) {
            throw new \Exception("No such entry");
        }
        $row = $res->fetch_assoc();
        $url = "{$base_href}{$code_href}aktuell.php?id={$id}";
        $json_url = json_encode($url);
        $html_author = $row['autor'];
        $json_author = json_encode($html_author);
        $html_title = $row['titel'];
        $json_title = json_encode($html_title);
        $iso_date = $row['datum'].'T'.$row['zeit'];
        $json_iso_date = json_encode($iso_date);
        $images = [];
        $image_index = 1;
        while (true) {
            $fixed_width_index = str_pad("{$image_index}", 3, "0", STR_PAD_LEFT);
            $image_relative_path = "img/aktuell/{$id}/img/{$fixed_width_index}.jpg";
            if (!is_file("{$data_path}{$image_relative_path}")) {
                break;
            }
            $images[] = "{$data_href}{$image_relative_path}";
            $image_index++;
        }
        $json_images = json_encode($images);
        return <<<ZZZZZZZZZZ
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Article",
            "identifier": {$json_url},
            "url": {$json_url},
            "author": {$json_author},
            "headline": {$json_title},
            "image": {$json_images},
            "datePublished": {$json_iso_date},
            "dateModified": {$json_iso_date}
        }
        </script>
        ZZZZZZZZZZ;
    }
}
