<?php

namespace Zoho\Crm\Entities;

/**
 * PotStageHistory entity class.
 */
class PotStageHistory extends AbstractEntity
{
    /** @inheritdoc */
    protected static $module_name = 'PotStageHistory';

    /** @inheritdoc */
    protected static $property_aliases = [
        'id' => 'POTENTIALSTAGEHISTORYID',
        'amount' => 'Amount',
        'stage' => 'Stage',
        'probability' => 'Probability',
        'expected_revenue' => 'Expected Revenue',
        'closing_date' => 'Close Date',
        'modified_by' => 'MODIFIEDBY',
        'modified_by_name' => 'Modified By',
        'last_modified_at' => 'Last Modified Time',
    ];
}
