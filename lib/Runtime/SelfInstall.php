<?php

namespace UserKit\Runtime;

use AD7six\Dsn\DbDsn;
use AD7six\Dsn\Dsn;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\StreamOutput;
use UserKit\Runtime\Exceptions\UserKitConfigException;
use UserKit\UserKit;

/**
 * Utility for self-installing, upgrading and configuring the UserKit database.
 */
class SelfInstall
{
    /**
     * The config data used to perform the self installation.
     *
     * @var Config
     */
    protected $config;

    /**
     * SelfInstall constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Converts the connection string (a PDO DSN) to a environment that can be used in Phinx configuration.
     *
     * @return array
     */
    protected function generatePhinxEnvironmentConfig(): array
    {
        /**
         * @var DbDsn $dsnParsed
         */
        $dsnParsed = Dsn::parse($this->config->getConnectionString());
        $dsnArray = $dsnParsed->toArray();

        $getDsnValue = function (string $key, string $defaultValue = '') use ($dsnArray): string {
            if (isset($dsnArray[$key]) && !empty($dsnArray[$key])) {
                return $dsnArray[$key];
            }

            return $defaultValue;
        };

        return [
            'adapter' => $dsnParsed->getScheme(),
            'host' => $getDsnValue('host', '127.0.0.1'),
            'name' => $getDsnValue('database', 'userkit'),
            'user' => $getDsnValue('user') . $getDsnValue('login'),
            'pass' => $getDsnValue('password'),
            'port' => $getDsnValue('port'),
            'charset' => 'utf8'
        ];
    }

    /**
     * Ensures the UserKit database is up-to-date, upgrading or installing it as necessary.
     */
    public function migrateDatabase(): void
    {
        try {
            // Prepare the Phinx configuration based on the provided database connection string
            $dirPhinxBase = UserKit::getLibraryPath() . '/db';
            $dirPhinxMigrations = "{$dirPhinxBase}/migrations";
            $dirPhinxSeeds = "{$dirPhinxBase}/seeds";

            $migConfig = new \Phinx\Config\Config([
                'paths' => [
                    'migrations' => $dirPhinxMigrations,
                    'seeds' => $dirPhinxSeeds
                ],
                'environments' => [
                    'default_migration_table' => 'userkit_database_version',
                    'default_database' => 'db_env',
                    'db_env' => $this->generatePhinxEnvironmentConfig()
                ]
            ]);

            // Perform the actual database migration
            $dummyInput = new StringInput('');
            $dummyOutput = new NullOutput();

            $migManager = new Manager($migConfig, $dummyInput, $dummyOutput);
            $migManager->migrate($migConfig->getDefaultEnvironment());
        } catch (\Exception $ex) {
            throw new UserKitConfigException("Database self-installation problem: {$ex->getMessage()}", $ex);
        }
    }
}