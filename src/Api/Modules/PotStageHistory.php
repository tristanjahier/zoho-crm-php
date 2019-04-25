<?php

namespace Zoho\Crm\Api\Modules;

/**
 * PotStageHistory module handler.
 */
class PotStageHistory extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $primary_key = 'POTENTIALSTAGEHISTORYID';

    /** @inheritdoc */
    protected static $associated_entity = \Zoho\Crm\Entities\PotStageHistory::class;

    /** @inheritdoc */
    protected static $supported_methods = [
        'getRelatedRecords',
    ];

    /**
     * Get the stage history of a potential.
     *
     * @param string $potential_id The potential ID
     * @return \Zoho\Crm\Entities\Collection
     */
    public function getPotentialStageHistory($potential_id)
    {
        return $this->relatedTo('Potentials', $potential_id)->get();
    }
}
