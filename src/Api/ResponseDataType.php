<?php

namespace Zoho\Crm\Api;

/**
 * Enumeration of the possible types of data returned by the API.
 */
abstract class ResponseDataType
{
    /** @var string A single record or multiple records */
    const RECORDS = 'records';

    /** @var string Anything else */
    const OTHER = 'other';
}
