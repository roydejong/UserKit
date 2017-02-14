<?php

namespace UserKit\Analytics;

use UserKit\Analytics\Events\ICaptureFlushedEventHandler;

/**
 * An analytics capturing transaction.
 */
class Capture
{
    /**
     * @var string
     */
    protected $requestUri;

    /**
     * @var string
     */
    protected $remoteAddress;

    /**
     * @var string
     */
    protected $userAgent;

    /**
     * @var ICaptureFlushedEventHandler[]
     */
    protected $flushedEventHandlers;

    /**
     * Creates a new analytics Capture.
     * Immediately begins capturing basic request data based on current PHP globals.
     */
    public function __construct()
    {
        $this->flushedEventHandlers = [];

        $this->captureRequestFromGlobals();
    }

    /**
     * Captures the current request based on current PHP globals.
     *
     * @return $this|Capture
     */
    protected function captureRequestFromGlobals(): Capture
    {
        // TODO Wrap in setters
        $this->requestUri = $_SERVER['REQUEST_URI'];
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
        $this->remoteAddress = $_SERVER['REMOTE_ADDR'];
        return $this;
    }

    /**
     * Registers a new event handler for the "capture flushed" event.
     *
     * @param ICaptureFlushedEventHandler $handler
     * @return $this|Capture
     */
    public function registerFlushedEventHandler(ICaptureFlushedEventHandler $handler): Capture
    {
        $this->flushedEventHandlers[] = $handler;
        return $this;
    }

    /**
     * Flushes and commits the current Capture data to the database.
     *
     * If this is the UserKit::capture() instance:
     *  - This capture will be flushed automatically on script shutdown. No need to do it manually.
     *  - After flushing, calling UserKit::capture() again will start a brand new capture.
     */
    public function flush(): void
    {
        // TODO Actual work

        foreach ($this->flushedEventHandlers as $eventHandler) {
            $eventHandler->onCaptureFlushed($this);
        }
    }
}