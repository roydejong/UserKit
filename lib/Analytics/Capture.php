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
        $this->setRequestUri($_SERVER['REQUEST_URI']);
        $this->setUserAgent($_SERVER['HTTP_USER_AGENT']);
        $this->setRemoteAddress($_SERVER['REMOTE_ADDR']);
        return $this;
    }

    /**
     * @param string $requestUri
     * @return $this|Capture
     */
    public function setRequestUri(string $requestUri): Capture
    {
        // TODO/IDEA: Option to strip out certain query string parameters
        $this->requestUri = $requestUri;
        return $this;
    }

    /**
     * @param string $userAgent
     * @return $this|Capture
     */
    public function setUserAgent(string $userAgent): Capture
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * @param string $remoteAddress
     * @return $this|Capture
     */
    public function setRemoteAddress(string $remoteAddress): Capture
    {
        $this->remoteAddress = $remoteAddress;
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