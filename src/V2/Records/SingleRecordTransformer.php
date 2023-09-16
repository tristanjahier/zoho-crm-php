<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\Contracts\RequestInterface;

/**
 * A transformer for responses that consist in a list of a single record.
 */
class SingleRecordTransformer implements ResponseTransformerInterface
{
    /**
     * @inheritdoc
     *
     * @return Record
     */
    public function transformResponse($content, RequestInterface $request)
    {
        if (empty($content['data'])) {
            return null;
        }

        return new Record($content['data'][0]);
    }
}
