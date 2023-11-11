<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Support\HttpMethod;

/**
 * A request to update many records.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/update-records.html
 */
class UpdateManyRequest extends InsertRequest
{
    /** @inheritdoc */
    protected $httpMethod = HttpMethod::PUT;
}
