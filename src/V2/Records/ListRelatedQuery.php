<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Contracts\PaginatedQueryInterface;
use Zoho\Crm\Contracts\ResponseTransformerInterface;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\V2\Traits\HasPagination;

class ListRelatedQuery extends AbstractQuery implements PaginatedQueryInterface
{
    use HasPagination;

    /**
     * @var string The record id
     */
    protected $recordId;

    /**
     * @var string The related list API name
     */
    protected $relatedList;

    /**
     * @inheritdoc
     */
    public function getUrl(): string
    {
        return "$this->module/$this->recordId/$this->relatedList";
    }

    /**
     * Get record Id.
     */
    public function getRecordId(): string
    {
        return $this->recordId;
    }

    /**
     * Set record Id.
     * @param string $id
     */
    public function setRecordId(string $id): void
    {
        $this->recordId = $id;
    }

    /**
     * Get related list API name.
     *
     * @return string
     */
    public function getRelatedList(): string
    {
        return $this->relatedList;
    }

    /**
     * Set related list API Name.
     *
     * @param string $relatedListName
     */
    public function setRelatedList(string $relatedListName): void
    {
        $this->relatedList = $relatedListName;
    }

    /**
     * Set the minimum modified time.
     *
     * @param \DateTimeInterface|string|null $date A date object or a valid string
     * @return $this
     *
     * @throws \Exception
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
     * @return \DateTimeInterface|string
     *
     * @throws \Exception
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

    /**
     * @inheritdoc
     */
    public function validate(): void
    {
        //
    }

    /**
     * @inheritdoc
     *
     * @return RecordListTransformer
     */
    public function getResponseTransformer(): ?ResponseTransformerInterface
    {
        return new RecordListTransformer();
    }
}
