<?php

namespace Olz\News\Utils;

use Olz\Entity\News\NewsEntry;
use Olz\Utils\WithUtilsTrait;

class NewsUtils {
    use WithUtilsTrait;

    /** @var array<string, string> */
    protected static $iconBasenameByFormat = [
        'aktuell' => 'entry_type_aktuell_20.svg',
        'forum' => 'entry_type_forum_20.svg',
        'galerie' => 'entry_type_gallery_20.svg',
        'kaderblog' => 'entry_type_kaderblog_20.svg',
        'video' => 'entry_type_movie_20.svg',

        'galerie_white' => 'entry_type_gallery_white_20.svg',
        'video_white' => 'entry_type_movie_white_20.svg',
    ];

    public function getNewsFormatIcon(
        NewsEntry|string $input,
        ?string $modifier = null,
    ): ?string {
        $format = $input instanceof NewsEntry ? $input->getFormat() : $input;
        $key = $modifier === null ? $format : "{$format}_{$modifier}";
        $icon = self::$iconBasenameByFormat[$key] ?? null;
        if ($icon === null) {
            return null;
        }
        $code_href = $this->envUtils()->getCodeHref();
        return "{$code_href}assets/icns/{$icon}";
    }

    public static function fromEnv(): self {
        return new self();
    }
}
