<?php

namespace Zoho\CRM\Modules;

use Zoho\CRM\Core\IdList;

class Leads extends AbstractModule
{
    protected $supported_methods = [
        'getFields',
        'getRecordById',
        'getRecords',
        'getMyRecords',
        'searchRecords'
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
        return $this->request('getMyRecords');
    }

    public function search($criteria)
    {
        return $this->request('searchRecords', ['criteria' => "($criteria)"]);
    }

    public function getBy($key, $value)
    {
        return $this->search(urlencode($key) . ':' . urlencode($value));
    }
}
