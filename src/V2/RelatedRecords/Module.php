<?php

namespace Zoho\Crm\V2\RelatedRecords;

use Zoho\Crm\V2\Client;

/**
 * A class to help querying the related records of a specific API module.
 */
class Module
{
    /** @var Client The client to which the module is linked */
    protected $client;

    /** @var string The name of the module */
    protected $name;

    /**
     * The constructor.
     *
     * @param Client $client The client to which the module is linked
     * @param string $name   The name of the module
     */
    public function __construct(Client $client, string $name)
    {
        $this->client = $client;
        $this->name = $name;
    }

    /**
     * Create a query to perform a related list among the records of the module.
     * @return ListQuery
     */
    public function newRelatedRecordQuery(): ListQuery
    {
        return new ListQuery($this->client, $this->name);
    }

    /**
     * Create a query to list the related records of the module.
     *
     * @param  string    $recordId        The record Id
     * @param  string    $relatedListName The related list API name
     *
     * @return ListQuery
     */
    public function getRelatedRecords(string $recordId, string $relatedListName): ListQuery
    {
        return $this->newRelatedRecordQuery()->setRelatedListRecord($recordId, $relatedListName)->autoPaginated();
    }
}
