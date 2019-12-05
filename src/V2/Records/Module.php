<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\V2\Client;

/**
 * A class to help querying the records of a specific API module.
 */
class Module
{
    /** @var \Zoho\Crm\V2\Client The client to which the module is linked */
    protected $client;

    /** @var string The name of the module */
    protected $name;

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\V2\Client $client The client to which the module is linked
     * @param string $name The name of the module
     */
    public function __construct(Client $client, string $name)
    {
        $this->client = $client;
        $this->name = $name;
    }

    /**
     * Create a query to list the records of a module.
     *
     * @return ListQuery
     */
    public function newListQuery()
    {
        return (new ListQuery($this->client))->setModule($this->name);
    }

    /**
     * Create a query to get a specific record by ID.
     *
     * @param string|null $id (optional) The record ID
     * @return GetByIdQuery
     */
    public function newGetByIdQuery(string $id = null)
    {
        $query = (new GetByIdQuery($this->client))->setModule($this->name);

        if (isset($id)) {
            $query->setId($id);
        }

        return $query;
    }

    /**
     * Alias of {@see self::newListQuery()}.
     *
     * @return ListQuery
     */
    public function all()
    {
        return $this->newListQuery();
    }

    /**
     * Retrieve a record by its ID.
     *
     * @param string $id The record ID
     * @return Record
     */
    public function find(string $id)
    {
        return $this->newGetByIdQuery($id)->get();
    }
}
