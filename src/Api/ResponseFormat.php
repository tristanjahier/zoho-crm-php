<?php

namespace Zoho\Crm\Api;

/**
 * Enumeration of the possible response formats returned by the API.
 */
abstract class ResponseFormat
{
    /** @var string JSON format */
    const JSON = 'json';

    /** @var string XML format */
    const XML = 'xml';
}
