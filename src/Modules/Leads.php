<?php

namespace Zoho\CRM\Modules;

use Zoho\CRM\Core\IdList;
use Zoho\CRM\Core\XmlBuilder;

class Leads extends AbstractModule
{
    protected $supported_methods = [
        'getFields',
        'getRecordById',
        'getRecords',
        'getMyRecords',
        'searchRecords',
        'insertRecords'
    ];

    public function getById($id)
    {
        return $this->request('getRecordById', ['id' => $id]);
    }

    public function getByIds(array $id_list)
    {
        return $this->request('getRecordById', ['idlist' => new IdList($id_list)]);
    }

    public function getMine()
    {
        return $this->request('getMyRecords', [], true);
    }

    public function search($criteria)
    {
        return $this->request('searchRecords', ['criteria' => "($criteria)"]);
    }

    public function getBy($key, $value)
    {
        return $this->search(urlencode($key) . ':' . urlencode($value));
    }

    public function insert($data)
    {
        return $this->request('insertRecords', [
            'version' => 4, // Required for full multiple records support
            'duplicateCheck' => 1,
            'xmlData' => XmlBuilder::buildRecords(self::getModuleName(), $data)
        ]);
    }
}
