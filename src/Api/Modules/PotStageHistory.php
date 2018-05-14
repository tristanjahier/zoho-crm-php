<?php

namespace Zoho\CRM\Api\Modules;

class PotStageHistory extends AbstractRecordsModule
{
    protected static $primary_key = 'POTENTIALSTAGEHISTORYID';

    protected static $associated_entity = \Zoho\CRM\Entities\PotStageHistory::class;

    protected static $supported_methods = [
        'getRelatedRecords',
    ];

    public function getPotentialHistory($potentialId)
    {
        return $this->getRelatedById('Potential', $potentialId)->fetch()->getContent();
    }
}
