<?php

namespace UserKit\Analytics\Utils;

use UserKit\Analytics\Capture;

/**
 * A Fingerprint can be generated based on an ongoing capture, and attempts to identify "unique" visitor sessions.
 *
 * Fingerprints are composed using environmental information contained within the request, such as:
 *  - Remote IP address
 *  - User agent string (Browser & OS)
 *  - Browser language preferences
 *  - DNT value
 *
 * In an ideal world, each fingerprint identifies a single, unique PC being used to access the site.
 *
 * We can somewhat reliably use fingerprints because their scope within UserKit is limited to day-to-day use, and not
 * for any kind of persistent tracking beyond that, making things such as browser version upgrades or dynamic IP
 * changes acceptable.
 */
class Fingerprint
{
    /**
     * @var array
     */
    protected $parts;

    /**
     * @var Capture
     */
    protected $capture;

    /**
     * Fingerprint constructor.
     *
     * @param Capture $capture The capture to base the fingerprint on.
     */
    public function __construct(Capture $capture)
    {
        $this->parts = [];
        $this->capture = $capture;

        $this->buildPartsFromCapture();
    }

    /**
     * Adds a value to the fingerprint.
     *
     * @param string $value
     */
    protected function addPart(string $value): void
    {
        if (empty($value)) {
            $value = 'N-A';
        }

        $this->parts[] = $value;
    }

    /**
     * Builds the $parts array based on the Capture data.
     */
    protected function buildPartsFromCapture(): void
    {
        $this->addPart($this->capture->getRemoteAddress());
        $this->addPart($this->capture->getUserAgent());
        $this->addPart($this->capture->getRequestHeader('Accept-Language'));
        $this->addPart($this->capture->getRequestHeader('DNT') == 1 ? 'DNT_ON' : 'DNT_OFF');
    }

    /**
     * Returns string representation of the fingerprint, as its raw source string before hashing.
     *
     * @return string
     */
    public function asPlainText(): string
    {
        $plain = '';

        foreach ($this->parts as $part) {
            if (!empty($plain)) {
                $plain .= "$";
            }

            $plain .= $part;
        }

        var_dump($plain);
        return $plain;
    }

    /**
     * Returns string representation of the fingerprint as SHA1 hash.
     *
     * @return string
     */
    public function asHash(): string
    {
        return sha1($this->asPlainText());
    }

    /**
     * Returns string representation of the fingerprint as SHA1 hash.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->asHash();
    }
}