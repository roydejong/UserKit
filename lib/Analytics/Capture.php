<?php

namespace UserKit\Analytics;

use ActiveRecord\DateTime;
use UserKit\Analytics\Events\ICaptureFlushedEventHandler;
use UserKit\Analytics\Utils\Fingerprint;
use UserKit\Analytics\Utils\UserAgent;
use UserKit\Models\UserkitVisitor;

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
     * @var ?string
     */
    protected $referer;

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
        $this->setReferer($this->getRequestHeader('Referer'));
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
     * @return UserAgent
     */
    public function getUserAgent(): UserAgent
    {
        return new UserAgent($this->userAgent);
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
     * @param string $referer
     * @return $this|Capture
     */
    public function setReferer(?string $referer): Capture
    {
        $this->referer = $referer;
        return $this;
    }

    /**
     * @return string
     */
    public function getReferer(): ?string
    {
        return $this->referer;
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
        // Prepare data for registration
        $now = new DateTime();
        $fingerprint = $this->getFingerprint();

        // Add to database, creating a new record or updating the previous fingerprint/date combo record
        // To clarify: Each unique visitor (identified by fingerprint) is only logged once per date
        // We prefer up-to-date info for most fields, but the top goal is to fill as many fields as possible
        $visitRecord = new UserkitVisitor();
        $visitRecord->fingerprint = $fingerprint;
        $visitRecord->date = $now->format('Y-m-d');

        $existingRecord = $visitRecord->getExisting();

        if ($existingRecord) {
            // Updating an existing record
            $visitRecord = $existingRecord;
        } else {
            // Creating a new record
            $visitRecord->page_views = 0;
        }

        $visitRecord->page_views++;

        if (!$visitRecord->agent_platform) {
            $visitRecord->agent_platform = $this->getUserAgent()->getPlatform();
        }

        if (!$visitRecord->agent_browser) {
            $visitRecord->agent_browser = $this->getUserAgent()->getBrowser();
        }

        if ($this->getReferer()) {
            $visitRecord->referer = $this->getReferer();
        }

        if (!$visitRecord->remote_address) {
            $visitRecord->remote_address = $this->getRemoteAddress();
        }

        $visitRecord->save();

        // Trigger event handlers for "flushed" event
        foreach ($this->flushedEventHandlers as $eventHandler) {
            $eventHandler->onCaptureFlushed($this);
        }
    }
}