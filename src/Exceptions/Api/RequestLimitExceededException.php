<?php

namespace Zoho\Crm\Exceptions\Api;

class RequestLimitExceededException extends AbstractException
{
    /** @inheritdoc */
    protected $description = 'Number of API calls exceeded.';

    /**
     * The constructor.
     *
     * @param string $message The message of the API error
     */
    public function __construct($message)
    {
        parent::__construct($message, '4421');
    }
}
