<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\Contracts\RequestInterface;
use Zoho\Crm\Entities\Collection;

/**
 * A transformer for responses that consist in a list of deleted records.
 */
class DeletedRecordListTransformer implements ResponseTransformerInterface
{
    /**
     * @inheritdoc
     *
     * @return \Zoho\Crm\Entities\Collection
     */
    public function transformResponse($content, RequestInterface $request)
    {
        $records = new Collection();

        if (is_null($content)) {
            return $records;
        }

        foreach ($content['data'] as $attributes) {
            $records->push(new DeletedRecord($attributes));
        }

        return $records;
    }
}
