<?php

namespace Olz\Apps;

use Olz\Entity\User;
use Olz\Utils\EnvUtils;
use Olz\Utils\WithUtilsTrait;

abstract class BaseAppMetadata {
    use WithUtilsTrait;

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

    public function getIconHref(): ?string {
        $code_href = $this->envUtils()->getCodeHref();
        $icon_path = $this->getIconPath();
        if (substr($icon_path, strlen($icon_path) - 4) == '.svg') {
            return "{$code_href}apps/{$this->getBasename()}/icon.svg";
        }
        if (substr($icon_path, strlen($icon_path) - 4) == '.png') {
            return "{$code_href}apps/{$this->getBasename()}/icon.png";
        }
        return "{$code_href}apps/?/icon.png";
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
        $out .= "<script type='text/javascript' src='{$js_href}' onload='olz{$basename}.loaded()'></script>";
        return $out;
    }
}
