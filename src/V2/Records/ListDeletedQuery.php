<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\Contracts\PaginatedQueryInterface;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\V2\Traits\HasPagination;

/**
 * A query to get a list of deleted records.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/get-deleted-records.html
 */
class ListDeletedQuery extends AbstractQuery implements PaginatedQueryInterface
{
    use HasPagination;

    /**
     * @inheritdoc
     */
    public function getUrl(): string
    {
        return "$this->module/deleted?$this->urlParameters";
    }

    /**
     * @inheritdoc
     *
     * @return DeletedRecordListTransformer
     */
    public function getResponseTransformer(): ?ResponseTransformerInterface
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
    public function after($date)
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
     * @return \DateTime
     *
     * @throws \InvalidArgumentException
     */
    protected function getValidatedDateObject($date)
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
