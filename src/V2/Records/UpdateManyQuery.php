<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Support\HttpMethod;

/**
 * A query to update many records.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/update-records.html
 */
class UpdateManyQuery extends InsertQuery
{
    /** @inheritdoc */
    protected $httpMethod = HttpMethod::PUT;
}
