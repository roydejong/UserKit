<?php

namespace UserKit;

use UserKit\Runtime\Config;

/**
 * Main API for the UserKit analytics library.
 */
class UserKit
{
    /**
     * @var Config
     */
    protected static $config;

    /**
     * Returns the Config object for UserKit.
     *
     * @return Config
     */
    public static function configure(): Config
    {
        if (!self::$config) {
            self::$config = new Config();
        }

        return self::$config;
    }
}