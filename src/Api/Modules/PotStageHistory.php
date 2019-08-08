<?php

namespace Zoho\Crm\Api\Modules;

/**
 * PotStageHistory module handler.
 */
class PotStageHistory extends AbstractRecordsModule
{
    /** @inheritdoc */
    protected static $associatedEntity = \Zoho\Crm\Entities\PotStageHistory::class;

    /** @inheritdoc */
    protected static $supportedMethods = [
        'getRelatedRecords',
    ];

    /**
     * Get the stage history of a potential.
     *
     * @param string $potentialId The potential ID
     * @return \Zoho\Crm\Entities\Collection
     */
    public function getPotentialStageHistory($potentialId)
    {
        return $this->relatedTo('Potentials', $potentialId)->get();
    }
}
