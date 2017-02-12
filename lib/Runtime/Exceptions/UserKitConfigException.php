<?php

namespace UserKit\Runtime\Exceptions;

use Exception;

/**
 * An Exception thrown when invalid configuration values are encountered.
 */
class UserKitConfigException extends \RuntimeException
{
    /**
     * The inner exception that cuased
     *
     * @var ?Exception
     */
    public $innerException;

    /**
     * UserKitConfigException constructor.
     *
     * @param string $message
     * @param Exception|null $innerException
     */
    public function __construct($message = "", ?Exception $innerException = null)
    {
        parent::__construct($message);

        $this->innerException = $innerException;
    }
}