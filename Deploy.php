<?php

use League\Flysystem\PhpseclibV2\SftpAdapter;
use League\Flysystem\PhpseclibV2\SftpConnectionProvider;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use PhpDeploy\AbstractDefaultDeploy;

require_once __DIR__.'/vendor/autoload.php';

class Deploy extends AbstractDefaultDeploy {
    use \Psr\Log\LoggerAwareTrait;

    protected function populateFolder() {
        $fs = new Symfony\Component\Filesystem\Filesystem();
        $build_folder_path = $this->getLocalBuildFolderPath();

        $this->logger->info("Remove jsbuild...");
        $fs->remove(__DIR__.'/public/_/jsbuild');
        $this->logger->info("Webpack build...");
        shell_exec('npm run webpack-build');
        $this->logger->info("Remove node_modules...");
        $fs->remove(__DIR__.'/node_modules');
        $this->logger->info("Copy to build...");
        $fs->mirror(__DIR__, $build_folder_path);

        // Zip live uploader, such that it can be downloaded as zip file.
        $this->logger->info("Zip live_results...");
        $results_path = "{$build_folder_path}/public/_/resultate";
        $live_uploader_path = "{$results_path}/live_uploader";
        $zip_path = "{$results_path}/live_uploader.zip";
        $zip = new \ZipArchive();
        $zip->open($zip_path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $directory = new \RecursiveDirectoryIterator($live_uploader_path);
        $iterator = new \RecursiveIteratorIterator($directory);
        foreach ($iterator as $item) {
            $filename = $item->getFileName();
            if ($filename !== '.' && $filename !== '..') {
                $real_path = $item->getRealPath();
                if ($real_path && is_file($real_path)) {
                    $relative_path = substr($real_path, strlen($live_uploader_path));
                    $zip->addFile($real_path, $relative_path);
                }
            }
        }
        $zip->close();

        $this->logger->info("Done populating build folder.");
    }

    protected function getFlysystemFilesystem() {
        $provider = new SftpConnectionProvider(
            'lx7.hoststar.hosting', // host (required)
            $this->username, // username (required)
            $this->password, // password (optional, default: null) set to null if privateKey is used
            null, // private key (optional, default: null) can be used instead of password, set to null if password is set
            null, // passphrase (optional, default: null), set to null if privateKey is not used or has no passphrase
            5544, // port (optional, default: 22)
            false, // use agent (optional, default: false)
            10, // timeout (optional, default: 10)
            4, // max tries (optional, default: 4)
        );
        $visibility = PortableVisibilityConverter::fromArray([
            'file' => [
                'public' => 0755,
                'private' => 0755,
            ],
            'dir' => [
                'public' => 0755,
                'private' => 0755,
            ],
        ]);
        $adapter = new SftpAdapter($provider, '/', $visibility);
        return new League\Flysystem\Filesystem($adapter);
    }

    public function getRemotePublicPath() {
        return 'public_html';
    }

    public function getRemotePublicUrl() {
        if ($this->environment === 'test') {
            return "https://test.olzimmerberg.ch";
        }
        if ($this->environment === 'prod') {
            return "https://olzimmerberg.ch";
        }
        throw new Exception("Environment must be `test` or `prod`");
    }

    public function getRemotePrivatePath() {
        return 'software_data';
    }

    public function install($public_path) {
        $fs = new Symfony\Component\Filesystem\Filesystem();

        $this->logger->info("Prepare for installation...");
        $fs->copy(__DIR__.'/../../.env.local', __DIR__.'/.env.local', true);
        $fs->mirror(__DIR__.'/vendor', __DIR__.'/public/_/config/vendor');

        $this->logger->info("Install...");
        $fs->copy(__DIR__.'/public/.htaccess', "{$public_path}/.htaccess", true);
        $index_path = "{$public_path}/index.php";
        $index_contents = file_get_contents(__DIR__.'/public/index.php');
        $updated_index_contents = str_replace(
            "require_once dirname(__DIR__).'/vendor/autoload_runtime.php';",
            "require_once dirname(__DIR__).'/{$this->getRemotePrivatePath()}/deploy/live/vendor/autoload_runtime.php';",
            $index_contents,
        );
        unlink($index_path);
        file_put_contents($index_path, $updated_index_contents);
        $fs->rename("{$public_path}/_", "{$public_path}/_old");
        $fs->rename(__DIR__.'/public/_', "{$public_path}/_");
        $fs->mkdir("{$public_path}/_/screenshots/generated");
        $fs->remove("{$public_path}/_old");
        $this->logger->info("Install done.");
    }
}

if (isset($_SERVER['argv']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    $deploy = new Deploy();
    $logger = new Logger('deploy');
    $logger->pushHandler(new ErrorLogHandler());
    $deploy->setLogger($logger);
    $deploy->cli();
}
