<?php

namespace Zoho\CRM\Exception\Api;

class InvalidParametersException extends AbstractException
{
    protected $description = 'Incorrect API parameter or API parameter value. Also check the method name and/or spelling errors in the API url.';

    public function __construct($message)
    {
        parent::__construct($message, '4600');
    }
}
