<?php

namespace Zoho\Crm\Api\Methods;

interface MethodWithPaginationInterface extends MethodInterface
{
    /**
     * Merge multiple API pages into a single response content.
     *
     * Useful to reduce all paginated records into a single collection.
     *
     * @param mixed[] ...$pages The responses content pages to merge
     * @return mixed
     */
    public function mergePaginatedContents(...$pages);
}
