<?php

namespace Zoho\Crm\Exceptions\Api;

class RecordNotFoundException extends AbstractException
{
    /** @inheritdoc */
    protected $description = 'No record available with the specified record ID.';

    /**
     * The constructor.
     *
     * @param string $message The message of the API error
     */
    public function __construct($message)
    {
        parent::__construct($message, '4103');
    }
}
