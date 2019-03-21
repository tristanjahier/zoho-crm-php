<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\Query;

interface MethodInterface
{
    public static function responseContainsData(array $response, Query $query);

    public static function tidyResponse(array $response, Query $query);

    public static function expectsMultipleRecords(Query $query);
}
