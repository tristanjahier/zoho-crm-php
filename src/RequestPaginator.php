<?php

declare(strict_types=1);

namespace Zoho\Crm;

class RequestPaginator implements Contracts\RequestPaginatorInterface
{
    /** @var int The maximum number of items per page */
    public const PAGE_MAX_SIZE = 200;

    /** @var Contracts\PaginatedRequestInterface The parent request */
    protected $request;

    /** @var bool Whether there is still data to fetch */
    protected $hasMoreData = true;

    /** @var int The latest page fetched */
    protected $latestPageFetched = 0;

    /**
     * The constructor.
     *
     * @param Contracts\PaginatedRequestInterface $request The parent request
     */
    public function __construct(Contracts\PaginatedRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Check if there is more data to fetch.
     *
     * There is no actual check, so if it returns true, it only means
     * that as far as we know, we have not fetched the last record/page yet.
     * The value is updated after each fetch.
     *
     * @return bool
     */
    public function hasMoreData(): bool
    {
        return $this->hasMoreData;
    }

    /**
     * @inheritdoc
     */
    public function getNextPageRequest(): Contracts\RequestInterface
    {
        return $this->request->copy()
            ->autoPaginated(false)
            ->param('page', ++$this->latestPageFetched);
    }

    /**
     * Handle a freshly retrieved page, perform checks, alter contents if needed.
     *
     * @param Contracts\ResponseInterface $page The page response
     * @return void
     */
    public function handlePage(Contracts\ResponseInterface $page): void
    {
        // If this page is empty, then the following ones will be too
        if ($page->isEmpty()) {
            $this->hasMoreData = false;
            return;
        }

        // If the page is not fully filled, it means we reached the end
        if (count($page->getContent()) < $this->getPageSize()) {
            $this->hasMoreData = false;
        }

        // Apply the "maximum modification date" limit.
        // @todo replace this tightly coupled implementation with a more generic feature.
        if (method_exists($this->request, 'modifiedBefore') && $this->request->hasMaxModificationDate()) {
            $lastEntityDate = new \DateTime($page->getContent()->last()->get('Modified_Time'));

            if ($lastEntityDate >= $this->request->getMaxModificationDate()) {
                $this->hasMoreData = false;
                $page->setContent($this->filterEntitiesExceedingMaxModificationDate($page->getContent()));
            }
        }
    }

    /**
     * Remove all entities from a page whose last modification date exceeds
     * the maximum date set in the request.
     *
     * @param \Zoho\Crm\Entities\Collection $entities The entities to filter
     * @return \Zoho\Crm\Entities\Collection
     */
    protected function filterEntitiesExceedingMaxModificationDate(Entities\Collection $entities)
    {
        return $entities->filter(function ($entity) {
            $modifiedAt = new \DateTime($entity->get('Modified_Time'));
            return $modifiedAt < $this->request->getMaxModificationDate();
        });
    }

    /**
     * Get the size of a page.
     *
     * @return int
     */
    protected function getPageSize(): int
    {
        return (int) ($this->request->getUrlParameter('per_page') ?? static::PAGE_MAX_SIZE);
    }
}
