<?php

declare(strict_types=1);

namespace Zoho\Crm\V2;

use Zoho\Crm\Contracts\ResponsePageMergerInterface;
use Zoho\Crm\Entities\Collection;

/**
 * Page merger for responses whose contents are collections of entities.
 */
class CollectionPageMerger implements ResponsePageMergerInterface
{
    /**
     * @inheritdoc
     */
    public function mergePaginatedContents(mixed ...$pages): Collection
    {
        $entities = new Collection();

        foreach ($pages as $page) {
            $entities = $entities->merge($page);
        }

        return $entities;
    }
}
