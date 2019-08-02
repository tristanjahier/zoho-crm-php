<?php

namespace Zoho\Crm\Api\Modules;

use Zoho\Crm\Api\IdList;
use Zoho\Crm\Api\XmlBuilder;

/**
 * Base class of the modules which handle Zoho records.
 */
abstract class AbstractRecordsModule extends AbstractModule
{
    /** @var string The name of the identifier field */
    protected static $primary_key;

    /**
     * Get the primary key / the identifier field of the module.
     *
     * @return string
     */
    public static function primaryKey()
    {
        return static::$primary_key;
    }

    /**
     * Create a query to get records.
     *
     * @see https://www.zoho.com/crm/developer/docs/api/getrecords.html
     *
     * @return \Zoho\Crm\Api\Query
     */
    public function all()
    {
        return $this->newQuery('getRecords', [], true);
    }

    /**
     * Retrieve a record by its ID.
     *
     * @see https://www.zoho.com/crm/developer/docs/api/getrecordbyid.html
     *
     * @param string $id The record ID
     * @return \Zoho\Crm\Entities\AbstractEntity|array
     */
    public function find($id)
    {
        return $this->newQuery('getRecordById', ['id' => $id])->get();
    }

    /**
     * Retrieve multiple records by their IDs.
     *
     * @see https://www.zoho.com/crm/developer/docs/api/getrecordbyid.html
     *
     * @param string[] $ids An array of record IDs
     * @return \Zoho\Crm\Entities\Collection|array[]
     */
    public function findMany($ids)
    {
        return $this->newQuery('getRecordById', ['idlist' => new IdList($ids)])->get();
    }

    /**
     * Create a query to get records owned by the auth token owner.
     *
     * @see https://www.zoho.com/crm/developer/docs/api/getmyrecords.html
     *
     * @return \Zoho\Crm\Api\Query
     */
    public function mine()
    {
        return $this->newQuery('getMyRecords', [], true);
    }

    /**
     * Create a query to search records based on given criteria.
     *
     * @see https://www.zoho.com/crm/developer/docs/api/searchrecords.html
     *
     * @param string $criteria The criteria string representation
     * @return \Zoho\Crm\Api\Query
     */
    public function search($criteria)
    {
        return $this->newQuery('searchRecords', ['criteria' => "($criteria)"], true);
    }

    /**
     * Create a query to search records by a given field.
     *
     * @see https://www.zoho.com/crm/developer/docs/api/searchrecords.html
     *
     * @param string $key The name of the field
     * @param string $value The value to search
     * @return \Zoho\Crm\Api\Query
     */
    public function searchBy($key, $value)
    {
        return $this->search("$key:$value");
    }

    /**
     * Create a query to get records related to another given record.
     *
     * @see https://www.zoho.com/crm/developer/docs/api/getrelatedrecords.html
     *
     * @param string $module The name of the parent module
     * @param string $id The ID of the related record
     * @return \Zoho\Crm\Api\Query
     */
    public function relatedTo($module, $id)
    {
        return $this->newQuery('getRelatedRecords', [
            'parentModule' => $module,
            'id' => $id
        ], true);
    }

    /**
     * Create a query to search records by a predefined column.
     *
     * @see https://www.zoho.com/crm/developer/docs/api/getsearchrecordsbypdc.html
     *
     * @param string $column The predefined column
     * @param string $value The value to search
     * @return \Zoho\Crm\Api\Query
     */
    public function searchByPredefinedColumn($column, $value)
    {
        return $this->newQuery('getSearchRecordsByPDC', [
            'searchColumn' => $column,
            'searchValue' => $value
        ], true);
    }

    /**
     * Check if a given ID matches an existing record.
     *
     * @param string $id The record ID
     * @return bool
     */
    public function exists($id)
    {
        return $this->find($id) !== null;
    }

    /**
     * Insert a new record.
     *
     * @see https://www.zoho.com/crm/developer/docs/api/insertrecords.html
     *
     * @param array $data The data of the record
     * @return (string|false)[] The inserted record ID or false if failed
     */
    public function insert($data)
    {
        return $this->insertMany([$data]);
    }

    /**
     * Insert multiple new records.
     *
     * @see https://www.zoho.com/crm/developer/docs/api/insertrecords.html
     *
     * @param array[] $data The data of the records
     * @return (string|false)[] The inserted records IDs or false if failed
     */
    public function insertMany($data)
    {
        return $this->newQuery('insertRecords', [
            'version' => 4, // Required for full multiple records support
            'duplicateCheck' => 1,
            'xmlData' => XmlBuilder::buildRecords(self::name(), $data)
        ])->get();
    }

    /**
     * Update an existing record.
     *
     * @see https://www.zoho.com/crm/developer/docs/api/updaterecords.html
     *
     * @param string $id The record ID
     * @param array $data The new data
     * @return string[]|false The updated record ID or false if failed
     */
    public function update($id, $data)
    {
        return $this->newQuery('updateRecords', [
            'version' => 2, // Required for single record support
            'id' => $id,
            'xmlData' => XmlBuilder::buildRecords(self::name(), [$data])
        ])->get();
    }

    /**
     * Update multiple existing records.
     *
     * @see https://www.zoho.com/crm/developer/docs/api/updaterecords.html
     *
     * @param array[] $data The new data
     * @return (string|false)[] The updated records IDs or false if failed
     */
    public function updateMany($data)
    {
        return $this->newQuery('updateRecords', [
            'version' => 4, // Required for full multiple records support
            'xmlData' => XmlBuilder::buildRecords(self::name(), $data)
        ])->get();
    }

    /**
     * Delete a record.
     *
     * @see https://www.zoho.com/crm/developer/docs/api/deleterecords.html
     *
     * @param string $id The record ID
     * @return null
     */
    public function delete($id)
    {
        return $this->newQuery('deleteRecords', ['id' => $id])->get();
    }

    /**
     * Delete multiple records.
     *
     * @see https://www.zoho.com/crm/developer/docs/api/deleterecords.html
     *
     * @param string[] $ids The records IDs
     * @return null
     */
    public function deleteMany($ids)
    {
        return $this->newQuery('deleteRecords', ['idlist' => new IdList($ids)])->get();
    }

    /**
     * Create a query to get the deleted records IDs.
     *
     * @see https://www.zoho.com/crm/developer/docs/api/getdeletedrecordids.html
     *
     * @return \Zoho\Crm\Api\Query
     */
    public function deletedIds()
    {
        return $this->newQuery('getDeletedRecordIds', [], true);
    }

    /**
     * Delete a file attached to a record.
     *
     * @see https://www.zoho.com/crm/developer/docs/api/deletefile.html
     *
     * @param string $attachment_id The attachment ID
     * @return bool
     */
    public function deleteAttachedFile($attachment_id)
    {
        return $this->newQuery('deleteFile', ['id' => $attachment_id])->get();
    }
}
