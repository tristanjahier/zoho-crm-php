<?php

namespace Zoho\Crm\Api\Exceptions;

class InvalidTicketIdException extends AbstractException
{
    /** @inheritdoc */
    protected $description = 'Invalid ticket. Also check if ticket has expired.';

    /**
     * The constructor.
     *
     * @param string $message The message of the API error
     */
    public function __construct($message)
    {
        parent::__construct($message, '4834');
    }
}
