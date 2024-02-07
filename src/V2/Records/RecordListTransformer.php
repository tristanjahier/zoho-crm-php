<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\RequestInterface;
use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\Entities\Collection;

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
    public function transformResponse(mixed $content, RequestInterface $request): Collection
    {
        $records = new Collection();

        if (is_null($content)) {
            return $records;
        }

        foreach ($content['data'] as $attributes) {
            $records->push(new Record($attributes));
        }

        return $records;
    }
}
