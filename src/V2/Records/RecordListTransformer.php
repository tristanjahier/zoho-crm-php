<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Entities\Collection;
use Zoho\Crm\Entities\Records\Record;

/**
 * A transformer for responses that consist in a list of records.
 */
class RecordListTransformer implements ResponseTransformerInterface
{
    /**
     * @inheritdoc
     *
     * @return \Zoho\Crm\Entities\Collection
     */
    public function transformResponse($content, QueryInterface $query)
    {
        $records = new Collection();

        foreach ($content['data'] as $attributes) {
            $records->push(new Record($attributes));
        }

        return $records;
    }
}
