<?php

namespace Zoho\Crm\Api\Exceptions;

class RateLimitExceededException extends AbstractException
{
    /** @inheritdoc */
    protected $description = 'API call cannot be completed as you have exceeded the "rate limit".';

    /**
     * The constructor.
     *
     * @param string $message The message of the API error
     */
    public function __construct($message)
    {
        parent::__construct($message, '4820');
    }
}
