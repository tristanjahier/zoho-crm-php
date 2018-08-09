<?php

namespace Zoho\Crm\Api\Modules;

class PotStageHistory extends AbstractRecordsModule
{
    protected static $primary_key = 'POTENTIALSTAGEHISTORYID';

    protected static $associated_entity = \Zoho\Crm\Entities\PotStageHistory::class;

    protected static $supported_methods = [
        'getRelatedRecords',
    ];

    public function getPotentialStageHistory($potential_id)
    {
        return $this->relatedTo('Potentials', $potential_id)->get();
    }
}
