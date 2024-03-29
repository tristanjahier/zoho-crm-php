<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\Records;

use DateTimeInterface;
use Zoho\Crm\Contracts\PaginatedRequestInterface;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\V2\Traits\HasPagination;

/**
 * A request to get a list of deleted records.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/get-deleted-records.html
 */
class ListDeletedRequest extends AbstractRequest implements PaginatedRequestInterface
{
    use HasPagination;

    /**
     * @inheritdoc
     */
    public function getUrlPath(): string
    {
        return "{$this->module}/deleted";
    }

    /**
     * @inheritdoc
     */
    public function getResponseTransformer(): DeletedRecordListTransformer
    {
        return new DeletedRecordListTransformer();
    }

    /**
     * Set the minimum deletion date.
     *
     * @param \DateTimeInterface|string|null $date A date object or a valid string
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function after(DateTimeInterface|string|null $date): static
    {
        if (is_null($date)) {
            return $this->removeHeader('If-Modified-Since');
        }

        $date = $this->getValidatedDateObject($date);

        return $this->setHeader('If-Modified-Since', $date->format(DATE_ATOM));
    }

    /**
     * Ensure to get a valid DateTime object.
     *
     * @param \DateTimeInterface|string $date A date object or a valid string
     *
     * @throws \InvalidArgumentException
     */
    protected function getValidatedDateObject(DateTimeInterface|string $date): DateTimeInterface
    {
        if (! Helper::isValidDateInput($date)) {
            throw new \InvalidArgumentException('Date must implement DateTimeInterface or be a valid date string.');
        }

        if (is_string($date)) {
            return new \DateTime($date);
        }

        return $date;
    }
}
