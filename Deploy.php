<?php

use League\Flysystem\PhpseclibV3\SftpAdapter;
use League\Flysystem\PhpseclibV3\SftpConnectionProvider;
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
        $fs->remove(__DIR__.'/public/jsbuild');
        $this->logger->info("Webpack build...");
        $commands = [
            'export NODE_OPTIONS="--max-old-space-size=4096"',
            'npm run webpack-build',
        ];
        shell_exec(implode(';', $commands));
        $this->logger->info("Remove node_modules...");
        $fs->remove(__DIR__.'/node_modules');
        $this->logger->info("Copy to build...");
        $fs->mirror(__DIR__, $build_folder_path);

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
        throw new \Exception("Environment must be `test` or `prod`");
    }

    public function getRemotePrivatePath() {
        return 'software_data';
    }

    public function install($public_path) {
        $fs = new Symfony\Component\Filesystem\Filesystem();

        $this->logger->info("Prepare for installation (env={$this->environment})...");
        $fs->copy(__DIR__.'/../../.env.local', __DIR__.'/.env.local', true);
        $fs->mirror(__DIR__.'/vendor', __DIR__.'/_/config/vendor');
        $fs->mkdir(__DIR__.'/_/screenshots/generated');

        $install_path = $public_path;
        $deploy_path_from_public_index = 'dirname(__DIR__)';
        if ($this->environment === 'test') {
            $entries = scandir($public_path);
            foreach ($entries as $entry) {
                $path = "{$public_path}/{$entry}";
                if ($entry[0] !== '.' && is_dir($path) && $fs->exists("{$path}/_TOKEN_DIR_WILL_BE_REMOVED.txt")) {
                    $fs->remove($path);
                }
            }
            $test_token = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(openssl_random_pseudo_bytes(18)));
            $this->logger->info("Test token: {$test_token}");
            $install_path = "{$public_path}/{$test_token}";
            $deploy_path_from_public_index = 'dirname(dirname(__DIR__))';
        }

        $this->logger->info("Install...");
        if (!$fs->exists($install_path)) {
            $fs->mkdir($install_path);
        }
        $fs->copy(__DIR__.'/public/.htaccess', "{$install_path}/.htaccess", true);
        $fs->mirror(__DIR__.'/public/icns', "{$install_path}/icns");
        $fs->mirror(__DIR__.'/public/bundles', "{$install_path}/bundles");
        $index_path = "{$install_path}/index.php";
        $index_contents = file_get_contents(__DIR__.'/public/index.php');
        $updated_index_contents = str_replace(
            "deploy_path = dirname(__DIR__);",
            "deploy_path = {$deploy_path_from_public_index}.'/{$this->getRemotePrivatePath()}/deploy/live';",
            $index_contents,
        );
        unlink($index_path);
        file_put_contents($index_path, $updated_index_contents);
        if ($fs->exists("{$install_path}/jsbuild")) {
            $fs->rename("{$install_path}/jsbuild", "{$install_path}/old_jsbuild");
        }
        $fs->rename(__DIR__.'/public/jsbuild', "{$install_path}/jsbuild");
        if ($fs->exists("{$install_path}/old_jsbuild")) {
            $fs->remove("{$install_path}/old_jsbuild");
        }
        if ($this->environment === 'test') {
            file_put_contents("{$install_path}/_TOKEN_DIR_WILL_BE_REMOVED.txt", '');
        }
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
