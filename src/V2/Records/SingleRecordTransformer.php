<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\RequestInterface;
use Zoho\Crm\Contracts\ResponseTransformerInterface;

/**
 * A transformer for responses that consist in a list of a single record.
 */
class SingleRecordTransformer implements ResponseTransformerInterface
{
    /**
     * @inheritdoc
     *
     * @return Record|null
     */
    public function transformResponse(mixed $content, RequestInterface $request): mixed
    {
        if (empty($content['data'])) {
            return null;
        }

        return new Record($content['data'][0]);
    }
}
