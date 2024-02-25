<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\RequestInterface;
use Zoho\Crm\Contracts\ResponseTransformerInterface;

/**
 * A very basic transformer to extract the value of the "data" key.
 */
class UnwrapDataTransformer implements ResponseTransformerInterface
{
    /**
     * @inheritdoc
     */
    public function transformResponse(mixed $content, RequestInterface $request): mixed
    {
        return $content['data'] ?? null;
    }
}
