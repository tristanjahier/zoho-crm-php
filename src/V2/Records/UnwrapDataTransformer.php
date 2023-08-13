<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\Contracts\QueryInterface;

/**
 * A very basic transformer to extract the value of the "data" key.
 */
class UnwrapDataTransformer implements ResponseTransformerInterface
{
    /**
     * @inheritdoc
     *
     * @return mixed|null
     */
    public function transformResponse($content, QueryInterface $query)
    {
        return $content['data'] ?? null;
    }
}
