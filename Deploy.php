<?php

use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use PhpDeploy\AbstractDefaultDeploy;

require_once __DIR__.'/vendor/autoload.php';

class MirroringFilter extends RecursiveFilterIterator {
    /** @var array<string, bool> */
    protected $excludes = [];

    /** @param array<string> $excludes */
    public function __construct(RecursiveDirectoryIterator $iterator, array $excludes) {
        parent::__construct($iterator);
        $this->excludes = [];
        foreach ($excludes as $exclude) {
            $this->excludes[$exclude] = true;
        }
    }

    public function accept() {
        return !($this->excludes[$this->current()->getFilename()] ?? false);
    }

    public function getChildren() {
        return new MirroringFilter($this->getInnerIterator()->getChildren(), $this->excludes);
    }
}

class Deploy extends AbstractDefaultDeploy {
    use Psr\Log\LoggerAwareTrait;

    protected $bot_access_token;

    public function initFromEnv() {
        parent::initFromEnv();

        $access_token = $this->getEnvironmentVariable('BOT_ACCESS_TOKEN');

        $this->bot_access_token = $access_token;
    }

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

        $this->logger->info("Copy to build...");
        $iterator = new RecursiveDirectoryIterator(__DIR__, FilesystemIterator::SKIP_DOTS);
        $iterator = new MirroringFilter($iterator, [
            '.git',
            '.github',
            '.vscode',
            'bin',
            'ci',
            'coverage',
            'docs/coverage',
            'node_modules',
            'php-coverage',
            'tests',
            'tscbuild',
        ]);
        $iterator = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
        $fs->mirror(__DIR__, $build_folder_path, $iterator);

        $this->logger->info("Done populating build folder: {$build_folder_path}");
    }

    protected function getFlysystemFilesystem() {
        if ($this->target === 'hosttech') {
            $options = FtpConnectionOptions::fromArray([
                'host' => '219.hosttech.eu', // required
                'root' => '/', // required
                'username' => $this->username, // required
                'password' => $this->password, // required
                'port' => 21,
                'ssl' => true,
                'timeout' => 90,
                'utf8' => false,
                'passive' => true,
                'transferMode' => FTP_BINARY,
                'systemType' => null, // 'windows' or 'unix'
                'ignorePassiveAddress' => null, // true or false
                'timestampsOnUnixListingsEnabled' => false, // true or false
                'recurseManually' => true, // true
            ]);
            $adapter = new FtpAdapter($options);
            return new League\Flysystem\Filesystem($adapter);
        }
        throw new Exception("Target must be `hosttech`");
    }

    public function getRemotePublicPath() {
        if ($this->target === 'hosttech') {
            if ($this->environment === 'staging') {
                return "httpdocsstaging";
            }
            if ($this->environment === 'prod') {
                return "httpdocs";
            }
            throw new Exception("Environment must be `staging` or `prod`");
        }
        throw new Exception("Target must be `hosttech`");
    }

    public function getRemotePublicUrl() {
        if ($this->target === 'hosttech') {
            if ($this->environment === 'staging') {
                return "https://staging.olzimmerberg.ch";
            }
            if ($this->environment === 'prod') {
                return "https://olzimmerberg.ch";
            }
            throw new Exception("Environment must be `staging` or `prod`");
        }
        throw new Exception("Target must be `hosttech`");
    }

    public function getRemotePrivatePath() {
        if ($this->target === 'hosttech') {
            if ($this->environment === 'staging') {
                return "private/staging";
            }
            if ($this->environment === 'prod') {
                return "private/prod";
            }
            throw new Exception("Environment must be `staging` or `prod`");
        }
        throw new Exception("Target must be `hosttech`");
    }

    public function install($public_path) {
        $fs = new Symfony\Component\Filesystem\Filesystem();

        $this->logger->info("Prepare for installation (env={$this->environment})...");
        ini_set('memory_limit', '500M');
        gc_collect_cycles();
        $fs->copy(__DIR__.'/../../.env.local', __DIR__.'/.env.local', true);
        $fs->copy(__DIR__.'/../../config.php', __DIR__.'/config/olz.php', true);
        $fs->copy(__DIR__.'/../../config.php', __DIR__."/config/olz.{$this->environment}.php", true);
        $fs->mkdir(__DIR__.'/_/screenshots/generated');
        file_put_contents(__DIR__.'/src/Utils/data/DATA_PATH', realpath($public_path));
        gc_collect_cycles();

        $public_url = $this->getRemotePublicUrl();
        $staging_token = null;
        $install_path = $public_path;
        $deploy_path_from_public_index = 'dirname(__DIR__)';
        if ($this->environment === 'staging') {
            $entries = scandir($public_path);
            foreach ($entries as $entry) {
                $path = "{$public_path}/{$entry}";
                if ($entry[0] !== '.' && is_dir($path) && $fs->exists("{$path}/_TOKEN_DIR_WILL_BE_REMOVED.txt")) {
                    $fs->remove($path);
                }
            }
            $staging_token = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(openssl_random_pseudo_bytes(18)));
            $this->logger->info("--------------------------------------------");
            $this->logger->info("   {$public_url}/{$staging_token}/   ");
            $this->logger->info("   {$public_url}/{$staging_token}/screenshots/   ");
            $this->logger->info("--------------------------------------------");
            $install_path = "{$public_path}/{$staging_token}";
            $deploy_path_from_public_index = 'dirname(dirname(__DIR__))';
        }

        $this->logger->info("Install...");
        if (!$fs->exists($install_path)) {
            $fs->mkdir($install_path);
        }
        if ($this->environment === 'staging') {
            file_put_contents("{$install_path}/_TOKEN_DIR_WILL_BE_REMOVED.txt", '');
        }
        $fs->mirror(__DIR__.'/assets', "{$public_path}/assets");
        $fs->copy(__DIR__.'/public/.htaccess', "{$install_path}/.htaccess", true);
        $fs->mirror(__DIR__.'/public/bundles', "{$install_path}/bundles");
        $index_path = "{$install_path}/index.php";
        $index_contents = file_get_contents(__DIR__.'/public/index.php');
        $updated_index_contents = str_replace(
            "deploy_path = dirname(__DIR__);",
            "deploy_path = {$deploy_path_from_public_index}.'/{$this->getRemotePrivatePath()}/deploy/live';",
            $index_contents,
        );
        if ($fs->exists($index_path)) {
            $fs->remove($index_path);
        }
        file_put_contents($index_path, $updated_index_contents);
        if ($fs->exists("{$public_path}/jsbuild")) {
            $fs->rename("{$public_path}/jsbuild", "{$public_path}/old_jsbuild");
        }
        $fs->rename(__DIR__.'/public/jsbuild', "{$public_path}/jsbuild");
        if ($fs->exists("{$public_path}/old_jsbuild")) {
            $fs->remove("{$public_path}/old_jsbuild");
        }
        $this->logger->info("Install done.");
        return [
            'staging_token' => $staging_token,
        ];
    }

    protected function afterDeploy($result) {
        $staging_token = $result['staging_token'];

        if ($this->environment === 'staging') {
            // Add doctrine:cache:clear-metadata?
            // Add doctrine:cache:clear-query?
            // Add doctrine:cache:clear-result?
            $this->executeCommand('cache:clear', null, $staging_token);
            $this->executeCommand('olz:db-reset', 'full', $staging_token);
            $this->executeCommand('olz:send-telegram-configuration', null, $staging_token);
        } elseif ($this->environment === 'prod') {
            $this->executeCommand('cache:clear');
            $this->executeCommand('olz:db-migrate');
        }
    }

    protected function executeCommand($command, $argv = null, $staging_token = null) {
        $public_url = $this->getRemotePublicUrl();
        $prefix = ($this->environment === 'staging')
            ? "{$public_url}/{$staging_token}"
            : "{$public_url}";
        $execute_command_url = "{$prefix}/api/executeCommand?access_token={$this->bot_access_token}";

        $this->logger->info("Executing \"{$command}\"...");
        $request = urlencode(json_encode(['command' => $command, 'argv' => $argv]));
        $output = file_get_contents("{$execute_command_url}&request={$request}");
        $data = json_decode($output, true) ?? [];
        $is_error = $output === false
            || !is_array($data)
            || ($data['error'] ?? true)
            || !((bool) $data['output'] ?? false);
        if ($is_error) {
            $this->logger->error($output);
            $this->logger->error("Error executing \"{$command}\".");
            throw new Exception("Error executing \"{$command}\".");
        }
        $this->logger->info($data['output'] ?? '(output empty)');
        $this->logger->info("Executing \"{$command}\" done.");
    }
}

if (isset($_SERVER['argv']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    $deploy = new Deploy();
    $logger = new Logger('deploy');
    $logger->pushHandler(new ErrorLogHandler());
    $deploy->setLogger($logger);
    $deploy->cli();
}
