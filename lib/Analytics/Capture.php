<?php

namespace UserKit\Analytics;

use UserKit\Analytics\Events\ICaptureFlushedEventHandler;
use UserKit\Analytics\Utils\Fingerprint;

/**
 * An analytics capturing transaction.
 */
class Capture
{
    /**
     * @var string[]
     */
    protected $requestHeaders;

    /**
     * @var ?string
     */
    protected $requestUri;

    /**
     * @var ?string
     */
    protected $remoteAddress;

    /**
     * @var ?string
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
        $this->requestHeaders = [];
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
        // Extract request headers from the $_SERVER superglobal
        $requestHeaders = [];

        foreach ($_SERVER as $key => $value) {
            // The keys for headers in the $_SERVER superglobal are formatted like e.g. "HTTP_CONTENT_TYPE".
            $headerPrefix = 'HTTP_';

            if (strpos($key, $headerPrefix) === 0) {
                $headerName = substr($key, strlen($headerPrefix));      // HTTP_CONTENT_TYPE    =>      CONTENT_TYPE
                $headerName = str_replace('_', '-', $headerName);       // CONTENT_TYPE         =>      CONTENT-TYPE

                $requestHeaders[$headerName] = $value;
            }
        }

        $this->setRequestHeaders($requestHeaders);

        $this->setRequestUri($_SERVER['REQUEST_URI']);
        $this->setRemoteAddress($_SERVER['REMOTE_ADDR']);

        $this->setUserAgent($this->getRequestHeader('User-Agent'));
        return $this;
    }

    /**
     * @param string[] $headers Associative array of request headers, headerName => headerValue.
     * @return $this|Capture
     */
    public function setRequestHeaders(array $headers): Capture
    {
        // Ensure all header names are uppercase for comparison purposes in getRequestHeader().
        $ucHeaders = [];

        foreach ($headers as $key => &$value) {
            $ucHeaders[strtoupper($key)] = $value;
        }

        $this->requestHeaders = $ucHeaders;
        return $this;
    }

    /**
     * Gets the value for a request header by its name.
     *
     * @param string $headerName Case-insensitive header name.
     * @return null|string Request header value, or NULL if the header isn't known.
     */
    public function getRequestHeader(string $headerName): ?string
    {
        $headerName = strtoupper($headerName);

        if (isset($this->requestHeaders[$headerName])) {
            return $this->requestHeaders[$headerName];
        }

        return null;
    }

    /**
     * Gets an array of all request headers.
     *
     * @return string[] Associative array of request headers, headerName => headerValue.
     */
    public function getRequestHeaders(): array
    {
        return $this->requestHeaders;
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
     * @return null|string
     */
    public function getRequestUri(): ?string
    {
        return $this->requestUri;
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
     * @return null|string
     */
    public function getUserAgent(): ?string
    {
        return $this->userAgent;
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
     * @return null|string
     */
    public function getRemoteAddress(): ?string
    {
        return $this->remoteAddress;
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
     * Calculates the fingerprint for this capture, which is used to identify a unique visitor.
     *
     * @return string
     */
    protected function getFingerprint(): string
    {
        $fingerprint = new Fingerprint($this);
        return $fingerprint->asHash();
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
        $visitorFingerprint = $this->getFingerprint();

        foreach ($this->flushedEventHandlers as $eventHandler) {
            $eventHandler->onCaptureFlushed($this);
        }
    }
}