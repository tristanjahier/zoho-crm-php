<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\V2\Client;

/**
 * An intermediate class to help creating requests for a specific API module.
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
     * Create a request to list the records of the module.
     *
     * @return ListRequest
     */
    public function newListRequest(): ListRequest
    {
        return new ListRequest($this->client, $this->name);
    }

    /**
     * Create a request to get a specific record by ID.
     *
     * @param string|null $id (optional) The record ID
     * @return GetByIdRequest
     */
    public function newGetByIdRequest(string $id = null): GetByIdRequest
    {
        $request = new GetByIdRequest($this->client, $this->name);

        if (isset($id)) {
            $request->setRecordId($id);
        }

        return $request;
    }

    /**
     * Create a request to list the deleted records of the module.
     *
     * @return ListDeletedRequest
     */
    public function newListDeletedRequest(): ListDeletedRequest
    {
        return new ListDeletedRequest($this->client, $this->name);
    }

    /**
     * Create a request to perform a search among the records of the module.
     *
     * @param string|null $criteria (optional) The search criteria
     * @return SearchRequest
     */
    public function newSearchRequest(string $criteria = null): SearchRequest
    {
        $request = new SearchRequest($this->client, $this->name);

        if (isset($criteria)) {
            $request->param('criteria', $criteria);
        }

        return $request;
    }

    /**
     * Create a request to list the records from another module related to a given record.
     *
     * @param string|null $recordId (optional) The record ID
     * @param string|null $relatedModule (optional) The name of the related module
     * @return ListRelatedRequest
     */
    public function newListRelatedRequest(string $recordId = null, string $relatedModule = null): ListRelatedRequest
    {
        $request = new ListRelatedRequest($this->client, $this->name);

        if (isset($recordId)) {
            $request->setRecordId($recordId);
        }

        if (isset($relatedModule)) {
            $request->setRelatedModule($relatedModule);
        }

        return $request;
    }

    /**
     * Create a request to insert one or many records.
     *
     * @param iterable|null $records (optional) The records to insert
     * @param array|null $triggers (optional) The triggers to enable
     * @return InsertRequest
     */
    public function newInsertRequest($records = null, array $triggers = null): InsertRequest
    {
        $request = new InsertRequest($this->client, $this->name);

        if (isset($records)) {
            $request->addRecords($records);
        }

        if (isset($triggers)) {
            $request->triggers($triggers);
        }

        return $request;
    }

    /**
     * Create a request to update a specific record by ID.
     *
     * @param string|null $id (optional) The record ID
     * @param array|Record|null $data (optional) The field values to update
     * @param array|null $triggers (optional) The triggers to enable
     * @return UpdateRequest
     */
    public function newUpdateRequest(string $id = null, $data = null, array $triggers = null): UpdateRequest
    {
        $request = new UpdateRequest($this->client, $this->name);

        if (isset($id)) {
            $request->setRecordId($id);
        }

        if (isset($data)) {
            $request->setRecordData($data);
        }

        if (isset($triggers)) {
            $request->triggers($triggers);
        }

        return $request;
    }

    /**
     * Create a request to update many records.
     *
     * @param iterable|null $records (optional) The records to update
     * @param array|null $triggers (optional) The triggers to enable
     * @return UpdateManyRequest
     */
    public function newUpdateManyRequest($records = null, array $triggers = null): UpdateManyRequest
    {
        $request = new UpdateManyRequest($this->client, $this->name);

        if (isset($records)) {
            $request->addRecords($records);
        }

        if (isset($triggers)) {
            $request->triggers($triggers);
        }

        return $request;
    }

    /**
     * Create a request to upsert (insert or update if exists) one or many records.
     *
     * @param iterable|null $records (optional) The records to upsert
     * @param array|null $duplicateCheckFields (optional) The fields used for duplicate check
     * @param array|null $triggers (optional) The triggers to enable
     * @return UpsertRequest
     */
    public function newUpsertRequest(
        $records = null,
        array $duplicateCheckFields = null,
        array $triggers = null
    ): UpsertRequest {
        $request = new UpsertRequest($this->client, $this->name);

        if (isset($records)) {
            $request->addRecords($records);
        }

        if (isset($duplicateCheckFields)) {
            $request->checkDuplicatesOn($duplicateCheckFields);
        }

        if (isset($triggers)) {
            $request->triggers($triggers);
        }

        return $request;
    }

    /**
     * Create a request to delete a specific record by ID.
     *
     * @param string|null $id (optional) The record ID
     * @return DeleteRequest
     */
    public function newDeleteRequest(string $id = null): DeleteRequest
    {
        $request = new DeleteRequest($this->client, $this->name);

        if (isset($id)) {
            $request->setRecordId($id);
        }

        return $request;
    }

    /**
     * Create a request to delete many records by ID.
     *
     * @param array|null $ids (optional) The records IDs
     * @return DeleteManyRequest
     */
    public function newDeleteManyRequest(array $ids = null): DeleteManyRequest
    {
        $request = new DeleteManyRequest($this->client, $this->name);

        if (isset($ids)) {
            $request->setRecordIds($ids);
        }

        return $request;
    }

    /**
     * Create an auto-paginated request to retrieve all the records.
     *
     * @return ListRequest
     */
    public function all(): ListRequest
    {
        return $this->newListRequest()->autoPaginated();
    }

    /**
     * Create an auto-paginated request to retrieve all the deleted records.
     *
     * @return ListDeletedRequest
     */
    public function deleted(): ListDeletedRequest
    {
        return $this->newListDeletedRequest()->autoPaginated();
    }

    /**
     * Create an auto-paginated request to search records matching some criteria.
     *
     * @param string $criteria The search criteria
     * @return SearchRequest
     */
    public function search(string $criteria): SearchRequest
    {
        return $this->newSearchRequest($criteria)->autoPaginated();
    }

    /**
     * Create an auto-paginated request to search records with a given field value.
     *
     * @param string $field The name of the field
     * @param string $value The wanted value
     * @return SearchRequest
     */
    public function searchBy(string $field, string $value): SearchRequest
    {
        return $this->search("({$field}:equals:{$value})");
    }

    /**
     * Create an auto-paginated request to list the records from another module related to a given record.
     *
     * @param string $recordId The record ID
     * @param string $relatedModule The name of the related module
     * @return ListRelatedRequest
     */
    public function relationsOf(string $recordId, string $relatedModule): ListRelatedRequest
    {
        return $this->newListRelatedRequest($recordId, $relatedModule)->autoPaginated();
    }

    /**
     * Create an auto-paginated request to list the records related to a given record from another module.
     *
     * Inverse of {@see self::relationsOf()}.
     *
     * @param string $relatedModule The name of the related module
     * @param string $recordId The related record ID
     * @return ListRelatedRequest
     */
    public function relatedTo(string $relatedModule, string $recordId): ListRelatedRequest
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
        return $this->newGetByIdRequest($id)->get();
    }

    /**
     * Insert a new record.
     *
     * @param array|Record $record The record to insert
     * @param array|null $triggers (optional) The triggers to enable
     * @return array|null
     */
    public function insert($record, array $triggers = null): ?array
    {
        $response = $this->newInsertRequest([$record], $triggers)->get();

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
    public function insertMany($records, array $triggers = null): array
    {
        return $this->newInsertRequest($records, $triggers)->get();
    }

    /**
     * Update an existing record.
     *
     * @param string $id The record ID
     * @param array|Record $data The field values to update
     * @param array|null $triggers (optional) The triggers to enable
     * @return array|null
     */
    public function update(string $id, $data, array $triggers = null): ?array
    {
        $response = $this->newUpdateRequest($id, $data, $triggers)->get();

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
    public function updateMany($records, array $triggers = null): array
    {
        return $this->newUpdateManyRequest($records, $triggers)->get();
    }

    /**
     * Upsert a record.
     *
     * @param array|Record $record The record to upsert
     * @param array|null $duplicateCheckFields (optional) The fields used for duplicate check
     * @param array|null $triggers (optional) The triggers to enable
     * @return array|null
     */
    public function upsert($record, array $duplicateCheckFields = null, array $triggers = null): ?array
    {
        $response = $this->newUpsertRequest([$record], $duplicateCheckFields, $triggers)->get();

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
    public function upsertMany($records, array $duplicateCheckFields = null, array $triggers = null): array
    {
        return $this->newUpsertRequest($records, $duplicateCheckFields, $triggers)->get();
    }

    /**
     * Delete a record by ID.
     *
     * @param string $id The ID of the record to delete
     * @return array|null
     */
    public function delete(string $id): ?array
    {
        $response = $this->newDeleteRequest($id)->get();

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
    public function deleteMany(array $ids): array
    {
        return $this->newDeleteManyRequest($ids)->get();
    }
}
