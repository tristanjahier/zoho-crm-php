<?php

namespace Zoho\CRM\Api\Modules;

class PotStageHistory extends AbstractRecordsModule
{
    protected static $primary_key = 'POTENTIALSTAGEHISTORYID';

    protected static $associated_entity = \Zoho\CRM\Entities\PotStageHistory::class;

    protected static $supported_methods = [
        'getRelatedRecords',
    ];

    public function getPotentialHistory($potential_id)
    {
        return $this->getRelatedById('Potential', $potential_id)->fetch()->getContent();
    }
}
