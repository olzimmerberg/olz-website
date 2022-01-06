<?php

use League\Flysystem\PhpseclibV2\SftpAdapter;
use League\Flysystem\PhpseclibV2\SftpConnectionProvider;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use PhpDeploy\AbstractDefaultDeploy;

require_once __DIR__.'/vendor/autoload.php';

class Deploy extends AbstractDefaultDeploy {
    protected function populateFolder() {
        $fs = new Symfony\Component\Filesystem\Filesystem();
        $build_folder_path = $this->getLocalBuildFolderPath();

        $fs->remove(__DIR__.'/src/jsbuild');
        shell_exec('npm run webpack-build');
        $fs->mirror(__DIR__.'/src', "{$build_folder_path}/web");

        // Zip live uploader, such that it can be downloaded as zip file.
        $results_path = "{$build_folder_path}/web/resultate";
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

        $fs->mirror(__DIR__.'/vendor', "{$build_folder_path}/vendor");
        $fs->copy(__DIR__.'/Deploy.php', "{$build_folder_path}/Deploy.php");
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

        $fs->mirror(__DIR__.'/web', "{$public_path}/deploy/candidate", null, ['delete' => true]);
        $fs->mirror(__DIR__.'/vendor', "{$public_path}/deploy/candidate/config/vendor", null, ['delete' => true]);
        $fs->mkdir("{$public_path}/deploy/candidate/screenshots/generated");

        try {
            $fs->remove("{$public_path}/deploy/previous");
        } catch (\Throwable $th) {
            // ignore
        }
        try {
            $fs->rename("{$public_path}/deploy/live", "{$public_path}/deploy/previous");
        } catch (\Throwable $th) {
            // ignore
        }
        $fs->rename("{$public_path}/deploy/candidate", "{$public_path}/deploy/live");
        $fs->remove("{$public_path}/_");
        $fs->symlink("{$public_path}/deploy/live", "{$public_path}/_");
    }
}

if (isset($_SERVER['argv']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    $deploy = new Deploy();
    $deploy->cli();
}
