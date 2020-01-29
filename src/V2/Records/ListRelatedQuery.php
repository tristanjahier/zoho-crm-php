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
     * @var string The record ID
     */
    protected $recordId;

    /**
     * @var string The related module API name.
     */
    protected $relatedModule;

    /**
     * @inheritdoc
     */
    public function getUrl(): string
    {
        return "$this->module/$this->recordId/$this->relatedModule";
    }

    /**
     * Get record ID.
     */
    public function getRecordId(): string
    {
        return $this->recordId;
    }

    /**
     * Set record ID.
     *
     * @param string $id
     */
    public function setRecordId(string $id): void
    {
        $this->recordId = $id;
    }

    /**
     * Get related module API name.
     *
     * @return string
     */
    public function getRelatedModule(): string
    {
        return $this->relatedModule;
    }

    /**
     * Set related module API Name.
     *
     * @param string $relatedModule
     */
    public function setRelatedModule(string $relatedModule): void
    {
        $this->relatedModule = $relatedModule;
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
