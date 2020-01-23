<?php

namespace Zoho\Crm\V2\RelatedRecords;

use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Entities\Collection;

/**
 * A transformer for responses that consist in a list of related records.
 */
class RelatedRecordListTransformer implements ResponseTransformerInterface
{
    /**
     * @inheritdoc
     *
     * @return Collection
     */
    public function transformResponse($content, QueryInterface $query)
    {
        $records = new Collection();

        if (is_null($content)) {
            return $records;
        }

        foreach ($content['data'] as $attributes) {
            $records->push(new RelatedRecord($attributes));
        }

        return $records;
    }
}
