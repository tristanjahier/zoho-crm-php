<?php

namespace Zoho\Crm\Contracts;

interface ResponsePageMergerInterface
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
