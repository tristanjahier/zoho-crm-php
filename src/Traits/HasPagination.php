<?php

declare(strict_types=1);

namespace Zoho\Crm\Traits;

/**
 * Basic implementation for PaginatedRequestInterface.
 */
trait HasPagination
{
    /** Whether the request must be automatically paginated or not */
    protected bool $autoPaginated = false;

    /** The maximum number of concurrent requests allowed to fetch pages */
    protected ?int $concurrency;

    /**
     * @inheritdoc
     */
    public function mustBePaginatedAutomatically(): bool
    {
        return $this->autoPaginated;
    }

    /**
     * @inheritdoc
     */
    public function disableAutomaticPagination(): void
    {
        $this->autoPaginated = false;
    }

    /**
     * @inheritdoc
     */
    public function mustBePaginatedConcurrently(): bool
    {
        return isset($this->concurrency) && $this->concurrency > 1;
    }

    /**
     * @inheritdoc
     */
    public function getConcurrency(): ?int
    {
        return $this->concurrency;
    }

    /**
     * Turn on/off automatic pagination for the request.
     *
     * If enabled, the pages will be automatically fetched on request execution.
     *
     * @param bool $enabled (optional) Whether the request is auto paginated, true if omitted
     * @return $this
     */
    public function autoPaginated(bool $enabled = true): static
    {
        $this->autoPaginated = $enabled;

        return $this;
    }

    /**
     * Set the concurrency limit for asynchronous pagination.
     *
     * @param int|null $concurrency The concurrency limit
     * @return $this
     */
    public function concurrency(?int $concurrency): static
    {
        if (! is_null($concurrency) && (! is_int($concurrency) || $concurrency <= 0)) {
            throw new \InvalidArgumentException('Request concurrency must be a positive non-zero integer.');
        }

        $this->concurrency = $concurrency;

        return $this;
    }
}
