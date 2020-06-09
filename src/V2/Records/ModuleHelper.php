<?php

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\V2\Client;

/**
 * An intermediate class to help creating queries for a specific API module.
 */
class ModuleHelper
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
     * Create a query to list the records of the module.
     *
     * @return ListQuery
     */
    public function newListQuery(): ListQuery
    {
        return new ListQuery($this->client, $this->name);
    }

    /**
     * Create a query to get a specific record by ID.
     *
     * @param string|null $id (optional) The record ID
     * @return GetByIdQuery
     */
    public function newGetByIdQuery(string $id = null): GetByIdQuery
    {
        $query = new GetByIdQuery($this->client, $this->name);

        if (isset($id)) {
            $query->setRecordId($id);
        }

        return $query;
    }

    /**
     * Create a query to list the deleted records of the module.
     *
     * @return ListDeletedQuery
     */
    public function newListDeletedQuery(): ListDeletedQuery
    {
        return new ListDeletedQuery($this->client, $this->name);
    }

    /**
     * Create a query to perform a search among the records of the module.
     *
     * @param string|null $criteria (optional) The search criteria
     * @return SearchQuery
     */
    public function newSearchQuery(string $criteria = null): SearchQuery
    {
        $query = new SearchQuery($this->client, $this->name);

        if (isset($criteria)) {
            $query->param('criteria', $criteria);
        }

        return $query;
    }

    /**
     * Create a query to list the records from another module related to a given record.
     *
     * @param string|null $recordId (optional) The record ID
     * @param string|null $relatedModule (optional) The name of the related module
     * @return ListRelatedQuery
     */
    public function newListRelatedQuery(string $recordId = null, string $relatedModule = null): ListRelatedQuery
    {
        $query = new ListRelatedQuery($this->client, $this->name);

        if (isset($recordId)) {
            $query->setRecordId($recordId);
        }

        if (isset($relatedModule)) {
            $query->setRelatedModule($relatedModule);
        }

        return $query;
    }

    /**
     * Create a query to insert one or many records.
     *
     * @param iterable|null $records (optional) The records to insert
     * @param array|null $triggers (optional) The triggers to enable
     * @return InsertQuery
     */
    public function newInsertQuery($records = null, array $triggers = null): InsertQuery
    {
        $query = new InsertQuery($this->client, $this->name);

        if (isset($records)) {
            $query->addRecords($records);
        }

        if (isset($triggers)) {
            $query->triggers($triggers);
        }

        return $query;
    }

    /**
     * Create a query to update a specific record by ID.
     *
     * @param string|null $id (optional) The record ID
     * @param array|Record|null $data (optional) The field values to update
     * @param array|null $triggers (optional) The triggers to enable
     * @return UpdateQuery
     */
    public function newUpdateQuery(string $id = null, $data = null, array $triggers = null): UpdateQuery
    {
        $query = new UpdateQuery($this->client, $this->name);

        if (isset($id)) {
            $query->setRecordId($id);
        }

        if (isset($data)) {
            $query->setRecordData($data);
        }

        if (isset($triggers)) {
            $query->triggers($triggers);
        }

        return $query;
    }

    /**
     * Create a query to update many records.
     *
     * @param iterable|null $records (optional) The records to update
     * @param array|null $triggers (optional) The triggers to enable
     * @return UpdateManyQuery
     */
    public function newUpdateManyQuery($records = null, array $triggers = null): UpdateManyQuery
    {
        $query = new UpdateManyQuery($this->client, $this->name);

        if (isset($records)) {
            $query->addRecords($records);
        }

        if (isset($triggers)) {
            $query->triggers($triggers);
        }

        return $query;
    }

    /**
     * Create a query to upsert (insert or update if exists) one or many records.
     *
     * @param iterable|null $records (optional) The records to upsert
     * @param array|null $duplicateCheckFields (optional) The fields used for duplicate check
     * @param array|null $triggers (optional) The triggers to enable
     * @return UpsertQuery
     */
    public function newUpsertQuery(
        $records = null,
        array $duplicateCheckFields = null,
        array $triggers = null
    ): UpsertQuery {
        $query = new UpsertQuery($this->client, $this->name);

        if (isset($records)) {
            $query->addRecords($records);
        }

        if (isset($duplicateCheckFields)) {
            $query->checkDuplicatesOn($duplicateCheckFields);
        }

        if (isset($triggers)) {
            $query->triggers($triggers);
        }

        return $query;
    }

    /**
     * Create a query to delete a specific record by ID.
     *
     * @param string|null $id (optional) The record ID
     * @return DeleteQuery
     */
    public function newDeleteQuery(string $id = null): DeleteQuery
    {
        $query = new DeleteQuery($this->client, $this->name);

        if (isset($id)) {
            $query->setRecordId($id);
        }

        return $query;
    }

    /**
     * Create a query to delete many records by ID.
     *
     * @param array|null $ids (optional) The records IDs
     * @return DeleteManyQuery
     */
    public function newDeleteManyQuery(array $ids = null): DeleteManyQuery
    {
        $query = new DeleteManyQuery($this->client, $this->name);

        if (isset($ids)) {
            $query->setRecordIds($ids);
        }

        return $query;
    }

    /**
     * Create an auto-paginated query to retrieve all the records.
     *
     * @return ListQuery
     */
    public function all(): ListQuery
    {
        return $this->newListQuery()->autoPaginated();
    }

    /**
     * Create an auto-paginated query to retrieve all the deleted records.
     *
     * @return ListDeletedQuery
     */
    public function deleted(): ListDeletedQuery
    {
        return $this->newListDeletedQuery()->autoPaginated();
    }

    /**
     * Create an auto-paginated query to search records matching some criteria.
     *
     * @param string $criteria The search criteria
     * @return SearchQuery
     */
    public function search(string $criteria): SearchQuery
    {
        return $this->newSearchQuery($criteria)->autoPaginated();
    }

    /**
     * Create an auto-paginated query to search records with a given field value.
     *
     * @param string $field The name of the field
     * @param string $value The wanted value
     * @return SearchQuery
     */
    public function searchBy(string $field, string $value): SearchQuery
    {
        return $this->search("($field:equals:$value)");
    }

    /**
     * Create an auto-paginated query to list the records from another module related to a given record.
     *
     * @param string $recordId The record ID
     * @param string $relatedModule The name of the related module
     * @return ListRelatedQuery
     */
    public function relationsOf(string $recordId, string $relatedModule): ListRelatedQuery
    {
        return $this->newListRelatedQuery($recordId, $relatedModule)->autoPaginated();
    }

    /**
     * Create an auto-paginated query to list the records related to a given record from another module.
     *
     * Inverse of {@see self::relationsOf()}.
     *
     * @param string $relatedModule The name of the related module
     * @param string $recordId The related record ID
     * @return ListRelatedQuery
     */
    public function relatedTo(string $relatedModule, string $recordId): ListRelatedQuery
    {
        return $this->client->records
            ->module($relatedModule)
            ->relationsOf($recordId, $this->name);
    }

    /**
     * Retrieve a specific record by ID.
     *
     * @param string $id The record ID
     * @return Record|null
     */
    public function find(string $id): ?Record
    {
        return $this->newGetByIdQuery($id)->get();
    }

    /**
     * Insert a new record.
     *
     * @param array|Record $record The record to insert
     * @param array|null $triggers (optional) The triggers to enable
     * @return array|null
     */
    public function insert($record, array $triggers = null)
    {
        $response = $this->newInsertQuery([$record], $triggers)->get();

        // Because we intended to explicitly insert only one record,
        // we want to return an individual response.
        return $response[0] ?? null;
    }

    /**
     * Insert new records.
     *
     * @param iterable $records The records to insert
     * @param array|null $triggers (optional) The triggers to enable
     * @return array[]
     */
    public function insertMany($records, array $triggers = null)
    {
        return $this->newInsertQuery($records, $triggers)->get();
    }

    /**
     * Update an existing record.
     *
     * @param string $id The record ID
     * @param array|Record $data The field values to update
     * @param array|null $triggers (optional) The triggers to enable
     * @return array|null
     */
    public function update(string $id, $data, array $triggers = null)
    {
        $response = $this->newUpdateQuery($id, $data, $triggers)->get();

        // Because we intended to explicitly update only one record,
        // we want to return an individual response.
        return $response[0] ?? null;
    }

    /**
     * Update many existing records.
     *
     * @param iterable $records The records to update
     * @param array|null $triggers (optional) The triggers to enable
     * @return array[]
     */
    public function updateMany($records, array $triggers = null)
    {
        return $this->newUpdateManyQuery($records, $triggers)->get();
    }

    /**
     * Upsert a record.
     *
     * @param array|Record $record The record to upsert
     * @param array|null $duplicateCheckFields (optional) The fields used for duplicate check
     * @param array|null $triggers (optional) The triggers to enable
     * @return array|null
     */
    public function upsert($record, array $duplicateCheckFields = null, array $triggers = null)
    {
        $response = $this->newUpsertQuery([$record], $duplicateCheckFields, $triggers)->get();

        // Because we intended to explicitly upsert only one record,
        // we want to return an individual response.
        return $response[0] ?? null;
    }

    /**
     * Upsert many records.
     *
     * @param iterable $records The records to upsert
     * @param array|null $duplicateCheckFields (optional) The fields used for duplicate check
     * @param array|null $triggers (optional) The triggers to enable
     * @return array[]
     */
    public function upsertMany($records, array $duplicateCheckFields = null, array $triggers = null)
    {
        return $this->newUpsertQuery($records, $duplicateCheckFields, $triggers)->get();
    }

    /**
     * Delete a record by ID.
     *
     * @param string $id The ID of the record to delete
     * @return array|null
     */
    public function delete(string $id)
    {
        $response = $this->newDeleteQuery($id)->get();

        // Because we intended to explicitly delete only one record,
        // we want to return an individual response.
        return $response[0] ?? null;
    }

    /**
     * Delete many records by ID.
     *
     * @param string[] $ids The IDs of the records to delete
     * @return array[]
     */
    public function deleteMany(array $ids)
    {
        return $this->newDeleteManyQuery($ids)->get();
    }
}
