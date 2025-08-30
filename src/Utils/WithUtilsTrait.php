<?php

namespace Olz\Utils;

use Olz\Captcha\Utils\CaptchaUtilsTrait;
use Olz\Fetchers\SolvFetcherTrait;
use Olz\News\Utils\NewsUtilsTrait;
use Olz\Suche\Utils\SearchUtilsTrait;
use Olz\Termine\Utils\TermineUtilsTrait;

trait WithUtilsTrait {
    use AuthUtilsTrait;
    use CaptchaUtilsTrait;
    use DateUtilsTrait;
    use DbUtilsTrait;
    use DevDataUtilsTrait;
    use EmailUtilsTrait;
    use EntityManagerTrait;
    use EntityUtilsTrait;
    use EnvUtilsTrait;
    use GeneralUtilsTrait;
    use HtmlUtilsTrait;
    use HttpUtilsTrait;
    use IdUtilsTrait;
    use ImageUtilsTrait;
    use LogTrait;
    use MapUtilsTrait;
    use MailerTrait;
    use MessageBusTrait;
    use NewsUtilsTrait;
    use SearchUtilsTrait;
    use SessionTrait;
    use SolvFetcherTrait;
    use SymfonyUtilsTrait;
    use TelegramUtilsTrait;
    use TermineUtilsTrait;
    use UploadUtilsTrait;

    // Legacy implementation
    // TODO: Migrate away!

    /**
     * @return array<string, string>
     */
    public function getParams(): array {
        return $this->getOrCreate('getParams');
    }

    /**
     * @return array<string, string>
     */
    public function createGetParams(): array {
        global $_GET;
        return $_GET;
    }

    /**
     * @param array<string, string> $getParams
     */
    public function setGetParams(array $getParams): void {
        WithUtilsCache::set('getParams', $getParams);
    }

    /**
     * @return array<string, mixed>
     */
    public function server(): array {
        return $this->getOrCreate('server');
    }

    /**
     * @return array<string, mixed>
     */
    public function createServer(): array {
        global $_SERVER;
        return $_SERVER;
    }

    /**
     * @param array<string, mixed> $server
     */
    public function setServer(array $server): void {
        WithUtilsCache::set('server', $server);
    }

    // ---

    /**
     * @return array<string, mixed>
     */
    public function getAllUtils(): array {
        return WithUtilsCache::getAll();
    }

    /**
     * @param array<string, mixed> $all_utils
     */
    public function setAllUtils(array $all_utils): void {
        WithUtilsCache::setAll($all_utils);
    }

    protected function getOrCreate(string $util_name): mixed {
        $util = WithUtilsCache::get($util_name);
        if ($util) {
            return $util;
        }
        $cap_util_name = ucfirst($util_name);
        $creator_name = "create{$cap_util_name}";
        $util = $this->{$creator_name}();
        WithUtilsCache::set($util_name, $util);
        return $util;
    }
}
