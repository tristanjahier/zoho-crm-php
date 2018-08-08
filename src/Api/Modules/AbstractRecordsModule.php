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

    public function getAll()
    {
        return $this->newQuery('getRecords', [], true)->get();
    }

    public function getById($id)
    {
        return $this->newQuery('getRecordById', ['id' => $id])->get();
    }

    public function getByIds(array $ids)
    {
        return $this->newQuery('getRecordById', ['idlist' => new IdList($ids)])->get();
    }

    public function getMine()
    {
        return $this->newQuery('getMyRecords', [], true)->get();
    }

    public function search($criteria)
    {
        return $this->newQuery('searchRecords', ['criteria' => "($criteria)"], true)->get();
    }

    public function getBy($key, $value)
    {
        return $this->search("$key:$value");
    }

    public function getRelatedById($module, $id)
    {
        return $this->newQuery('getRelatedRecords', [
            'parentModule' => $module,
            'id' => $id
        ], true)->get();
    }

    public function getByPredefinedColumn($column, $value)
    {
        return $this->newQuery('getSearchRecordsByPDC', [
            'searchColumn' => $column,
            'searchValue' => $value
        ], true)->get();
    }

    public function exists($id)
    {
        return $this->getById($id) !== null;
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

    public function deleteMany(array $ids)
    {
        return $this->newQuery('deleteRecords', ['idlist' => new IdList($ids)])->get();
    }

    public function getDeletedIds()
    {
        return $this->newQuery('getDeletedRecordIds', [], true)->get();
    }

    public function deleteAttachedFile($attachment_id)
    {
        return $this->newQuery('deleteFile', ['id' => $attachment_id])->get();
    }
}
