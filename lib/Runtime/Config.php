<?php

namespace UserKit\Runtime;
use UserKit\Runtime\Exceptions\UserKitConfigException;

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
        try {
            /**
             * @var $configObject \ActiveRecord\Config
             */
            $configObject = \ActiveRecord\Config::instance();
            $configObject->set_connections(['db' => $this->connectionString]);
            $configObject->set_default_connection('db');
        }
        catch (\Exception $ex) {
            throw new UserKitConfigException("Database configuration problem: {$ex->getMessage()}", $ex);
        }
    }
}