<?php

namespace UserKit;

use UserKit\Analytics\Capture;
use UserKit\Analytics\Events\ICaptureFlushedEventHandler;
use UserKit\Runtime\Config;
use UserKit\WebUi\WebUi;

/**
 * Main API for the UserKit analytics library.
 */
class UserKit
{
    /**
     * Tracks whether UserKit has been bootstrapped yet.
     *
     * @var bool
     */
    protected static $isBootstrapped = false;

    /**
     * The configuration data for UserKit.
     *
     * @var Config
     */
    protected static $config = null;

    /**
     * The current analytics request being captured.
     *
     * @var Capture
     */
    protected static $capture = null;

    /**
     * The web interface provider.
     *
     * @var WebUi
     */
    protected static $webUi = null;

    /**
     * Gets the absolute path to UserKit's installation directory.
     *
     * @return string
     */
    public static function getLibraryPath(): string
    {
        return realpath(__DIR__ . "/../");
    }

    /**
     * Bootstraps UserKit. Called when configure() is first called by the implementing application.
     */
    protected static function bootstrap(): void
    {
        self::$isBootstrapped = true;

        // Register a shutdown function to flush captures to database when the script is done executing. This even works
        // when exit() is called, so pretty much any time the script doesn't crash with a fatal error, which is fine for
        // our purposes.
        register_shutdown_function(function () {
            // But only if we actually already have a capture that started and that needs flushing.
            // We don't want to force analytics capturing.
            if (self::$capture) {
                self::$capture->flush();
            }
        });
    }

    /**
     * Returns the Config object for UserKit.
     *
     * @return Config
     */
    public static function configure(): Config
    {
        if (!self::$isBootstrapped) {
            self::bootstrap();
        }

        if (!self::$config) {
            self::$config = new Config();
        }

        return self::$config;
    }

    /**
     * Begins capturing the current request for Analytical purposes.
     *
     * @param bool $reset If true, clears any previous capture data without flushing it.
     * @return Capture
     */
    public static function capture(bool $reset = false): Capture
    {
        if (!self::$capture || $reset) {
            self::$capture = new Capture();

            // Register handler for the "capture flushed" event, so we can reset our active capture.
            self::$capture->registerFlushedEventHandler(new class extends UserKit implements ICaptureFlushedEventHandler
            {
                /**
                 * @inheritdoc
                 */
                public function onCaptureFlushed(Capture $capture): void
                {
                    self::$capture = null;
                }
            });
        }

        return self::$capture;
    }

    /**
     * Gets the Web interface provider.
     *
     * @return WebUi
     */
    public static function webui(): WebUi
    {
        if (!self::$webUi) {
            self::$webUi = new WebUi();
        }

        return self::$webUi;
    }
}