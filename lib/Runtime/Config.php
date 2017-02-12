<?php

namespace UserKit\Runtime;
use UserKit\Runtime\Exceptions\UserKitConfigException;
use UserKit\UserKit;

/**
 * Manages configuration options for UserKit.
 */
class Config
{
    /**
     * The database connection string, which is used to configure the database ORM.
     *
     * Example: "mysql://user:password@host/dbname?charset=utf8"
     *
     * @var string
     */
    protected $connectionString;

    /**
     * Gets the database connection string.
     *
     * @return string
     */
    public function getConnectionString(): string
    {
        return $this->connectionString;
    }

    /**
     * Sets the database connection string.
     *
     * @param string $connectionString The database connection string, e.g. "mysql://user:password@host/dbname?charset=utf8".
     * @return $this|Config
     */
    public function setConnectionString(string $connectionString): Config
    {
        $this->connectionString = $connectionString;
        $this->applyDatabaseConfiguration();
        return $this;
    }

    /**
     * Applies the database configuration to the underlying ORM provider.
     */
    protected function applyDatabaseConfiguration(): void
    {
        // Apply the configuration to the ORM library, this should catch most issues with connection strings
        try {
            /**
             * @var $configObject \ActiveRecord\Config
             */
            $configObject = \ActiveRecord\Config::instance();
            $configObject->set_connections(['db' => $this->connectionString]);
            $configObject->set_default_connection('db');
            $configObject->set_model_directory(UserKit::getLibraryPath() . '/lib/Models');
        }
        catch (\Exception $ex) {
            throw new UserKitConfigException("Database configuration problem: {$ex->getMessage()}", $ex);
        }

        // Attempt to install or upgrade the database using our Phinx migrations
        $databaseInstaller = new SelfInstall($this);
        $databaseInstaller->migrateDatabase();
    }
}