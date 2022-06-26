<?php

namespace Olz\Apps;

use Olz\Entity\User;
use Olz\Utils\EnvUtils;

abstract class BaseAppMetadata {
    abstract public function getDisplayName(): string;

    abstract public function getPath(): string;

    abstract public function getHref(): string;

    abstract public function isAccessibleToUser(User $user): bool;

    public function getBasename(): string {
        return basename($this->getPath());
    }

    public function getIconPath(): ?string {
        $app_path = $this->getPath();
        $svg_path = "{$app_path}/icon.svg";
        if (is_file($svg_path)) {
            return $svg_path;
        }
        $png_path = "{$app_path}/icon.png";
        if (is_file($png_path)) {
            return $png_path;
        }
        return null;
    }

    public function getIcon(): ?string {
        $icon_path = $this->getIconPath();
        if (!$icon_path) {
            $base64 = base64_encode(file_get_contents(__DIR__.'/default_icon.svg'));
            $mime_type = 'image/svg+xml';
            return "data:{$mime_type};base64,{$base64}";
        }
        $base64 = base64_encode(file_get_contents($icon_path));
        if (substr($icon_path, strlen($icon_path) - 4) == '.svg') {
            $mime_type = 'image/svg+xml';
            return "data:{$mime_type};base64,{$base64}";
        }
        if (substr($icon_path, strlen($icon_path) - 4) == '.png') {
            $mime_type = 'image/png';
            return "data:{$mime_type};base64,{$base64}";
        }
    }

    public function getJsCssImports() {
        $env_utils = EnvUtils::fromEnv();
        $data_path = $env_utils->getDataPath();
        $basename = $this->getBasename();
        $css_path = "{$data_path}jsbuild/app-{$basename}/main.min.css";
        $js_path = "{$data_path}jsbuild/app-{$basename}/main.min.js";
        $css_modified = is_file($css_path) ? filemtime($css_path) : 0;
        $js_modified = is_file($js_path) ? filemtime($js_path) : 0;
        $css_href = "/jsbuild/app-{$basename}/main.min.css?modified={$css_modified}";
        $js_href = "/jsbuild/app-{$basename}/main.min.js?modified={$js_modified}";

        $out = '';
        $out .= "<link rel='stylesheet' href='{$css_href}' />";
        $out .= "<script type='text/javascript' src='{$js_href}' onload='olz.loaded()'></script>";
        return $out;
    }
}
