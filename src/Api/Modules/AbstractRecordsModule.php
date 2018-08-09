<?php

namespace Zoho\Crm\Api\Modules;

use Zoho\Crm\Api\IdList;
use Zoho\Crm\Api\XmlBuilder;

abstract class AbstractRecordsModule extends AbstractModule
{
    protected static $primary_key;

    public static function primaryKey()
    {
        return static::$primary_key;
    }

    public function all()
    {
        return $this->newQuery('getRecords', [], true);
    }

    public function find($id)
    {
        return $this->newQuery('getRecordById', ['id' => $id])->get();
    }

    public function findMany($ids)
    {
        return $this->newQuery('getRecordById', ['idlist' => new IdList($ids)])->get();
    }

    public function mine()
    {
        return $this->newQuery('getMyRecords', [], true);
    }

    public function search($criteria)
    {
        return $this->newQuery('searchRecords', ['criteria' => "($criteria)"], true);
    }

    public function searchBy($key, $value)
    {
        return $this->search("$key:$value");
    }

    public function relatedTo($module, $id)
    {
        return $this->newQuery('getRelatedRecords', [
            'parentModule' => $module,
            'id' => $id
        ], true);
    }

    public function searchByPredefinedColumn($column, $value)
    {
        return $this->newQuery('getSearchRecordsByPDC', [
            'searchColumn' => $column,
            'searchValue' => $value
        ], true);
    }

    public function exists($id)
    {
        return $this->find($id) !== null;
    }

    public function insert($data)
    {
        return $this->insertMany([$data]);
    }

    public function insertMany($data)
    {
        return $this->newQuery('insertRecords', [
            'version' => 4, // Required for full multiple records support
            'duplicateCheck' => 1,
            'xmlData' => XmlBuilder::buildRecords(self::name(), $data)
        ])->get();
    }

    public function update($id, $data)
    {
        return $this->newQuery('updateRecords', [
            'version' => 2, // Required for single record support
            'id' => $id,
            'xmlData' => XmlBuilder::buildRecords(self::name(), [$data])
        ])->get();
    }

    public function updateMany($data)
    {
        return $this->newQuery('updateRecords', [
            'version' => 4, // Required for full multiple records support
            'xmlData' => XmlBuilder::buildRecords(self::name(), $data)
        ])->get();
    }

    public function delete($id)
    {
        return $this->newQuery('deleteRecords', ['id' => $id])->get();
    }

    public function deleteMany($ids)
    {
        return $this->newQuery('deleteRecords', ['idlist' => new IdList($ids)])->get();
    }

    public function deletedIds()
    {
        return $this->newQuery('getDeletedRecordIds', [], true);
    }

    public function deleteAttachedFile($attachment_id)
    {
        return $this->newQuery('deleteFile', ['id' => $attachment_id])->get();
    }
}
