<?php

namespace Zoho\Crm\Modules;

/**
 * Deals module handler.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/modules-fields.html#Deals
 */
class Deals extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $associatedEntity = \Zoho\Crm\Entities\Records\Deal::class;

    /** @inheritdoc */
    protected static $supportedMethods = [
        'getFields',
        'getRecordById',
        'getRecords',
        'getMyRecords',
        'searchRecords',
        'insertRecords',
        'updateRecords',
        'deleteRecords',
        'getDeletedRecordIds',
        'getRelatedRecords',
        'getSearchRecordsByPDC',
        'deleteFile',
    ];

    /**
     * Create a query to get the stage history of a deal.
     *
     * @param string $id The deal ID
     * @return \Zoho\Crm\Query
     */
    public function stageHistoryOf(string $id)
    {
        return $this->relationsOf($id, PotStageHistory::name());
    }

    /**
     * Create a query to get the contact roles related to a deal.
     *
     * @param string $id The deal ID
     * @return \Zoho\Crm\Query
     */
    public function contactRolesOf(string $id)
    {
        return $this->relationsOf($id, ContactRoles::name());
    }
}
