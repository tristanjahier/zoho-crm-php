<?php

namespace Zoho\Crm\Api\Exceptions;

class InvalidParametersException extends AbstractException
{
    /** @inheritdoc */
    protected $description = 'Incorrect API parameter or API parameter value. Also check the method name and/or spelling errors in the API url.';

    /**
     * The constructor.
     *
     * @param string $message The message of the API error
     */
    public function __construct($message)
    {
        parent::__construct($message, '4600');
    }
}
