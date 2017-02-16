<?php

namespace UserKit\Analytics\Utils;

use BrowscapPHP\Browscap;

/**
 * Utility for extracting information from user agent strings.
 */
class UserAgent
{
    /**
     * @var string
     */
    protected $agentString;

    /**
     * @var Browscap
     */
    protected $browsCap;

    /**
     * @var \stdClass
     */
    protected $browserData;

    /**
     * UserAgentParser constructor.
     *
     * @param string $userAgentString
     */
    public function __construct(string $userAgentString)
    {
        $this->agentString = $userAgentString;
        $this->browsCap = new Browscap();
        $this->browserData = $this->browsCap->getBrowser($this->agentString);
    }

    /**
     * @return string
     */
    public function getBrowser(): string
    {
        return $this->browserData->browser;
    }

    /**
     * @return string
     */
    public function getPlatform(): string
    {
        return $this->browserData->platform;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->agentString;
    }
}