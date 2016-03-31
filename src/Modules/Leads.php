<?php

namespace Zoho\CRM\Modules;

class Leads extends AbstractModule
{
    protected $supported_methods = [
        'getMyRecords',
        'getRecords',
        'getRecordById',
        'getDeletedRecordIds',
        'insertRecords',
        'updateRecords',
        'getSearchRecordsByPDC',
        'deleteRecords',
        'convertLead',
        'getRelatedRecords',
        'getFields',
        'updateRelatedRecords',
        'getUsers',
        'uploadFile',
        'delink',
        'downloadFile',
        'deleteFile',
        'uploadPhoto',
        'downloadPhoto',
        'deletePhoto',
        'getModules',
        'searchRecords'
    ];

    public function __construct($owner)
    {
        parent::__construct($owner);
    }

    public function getById($id)
    {
        return $this->request('getRecordById', ['id' => $id]);
    }

    public function getMine()
    {
        return $this->request('getMyRecords');
    }
}
