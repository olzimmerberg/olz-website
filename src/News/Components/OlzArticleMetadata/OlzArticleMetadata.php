<?php

namespace Olz\News\Components\OlzArticleMetadata;

use Olz\Components\Common\OlzComponent;

/** @extends OlzComponent<array<string, mixed>> */
class OlzArticleMetadata extends OlzComponent {
    public function getHtml(mixed $args): string {
        $db = $this->dbUtils()->getDb();
        $data_href = $this->envUtils()->getDataHref();
        $base_href = $this->envUtils()->getBaseHref();
        $code_href = $this->envUtils()->getCodeHref();

        $id = intval($args['id']);
        $sql = "SELECT author_name, title, published_date, published_time, image_ids FROM news WHERE id='{$id}'";
        $res = $db->query($sql);
        // @phpstan-ignore-next-line
        if ($res->num_rows == 0) {
            throw new \Exception("No such entry");
        }
        // @phpstan-ignore-next-line
        $row = $res->fetch_assoc();
        $url = "{$base_href}{$code_href}news/{$id}";
        $json_url = json_encode($url);
        // @phpstan-ignore-next-line
        $html_author = $row['author_name'];
        $json_author = json_encode($html_author);
        // @phpstan-ignore-next-line
        $html_title = $row['title'];
        $json_title = json_encode($html_title);
        // @phpstan-ignore-next-line
        $iso_date = $row['published_date'].'T'.$row['published_time'];
        $json_iso_date = json_encode($iso_date);
        // @phpstan-ignore-next-line
        $image_ids = json_decode($row['image_ids'] ?? '[]', true);
        $images = array_map(function ($image_id) use ($base_href, $data_href, $id) {
            return "{$base_href}{$data_href}img/news/{$id}/img/{$image_id}";
        }, $image_ids);
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
