<?php

namespace UserKit\WebUi;

/**
 * Incoming request to the Web UI.
 */
class Request
{
    /**
     * @var string
     */
    public $requestMethod;

    /**
     * @var \stdClass
     */
    public $payload;

    /**
     * @var string
     */
    public $type;

    /**
     * Reads a payload value.
     *
     * @param string $key The key to open on the payload object.
     * @param mixed $defaultValue The default value to return if $key is not found.
     * @return mixed
     */
    public function getValue(string $key, $defaultValue = null)
    {
        if (isset($this->payload->$key)) {
            return $this->payload->$key;
        }

        return $defaultValue;
    }
}