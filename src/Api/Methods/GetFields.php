<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\ResponseDataType;
use Zoho\Crm\Api\Query;

/**
 * @see https://www.zoho.com/crm/developer/docs/api/getfields.html
 */
class GetFields extends AbstractMethod
{
    /** @inheritdoc */
    protected static $responseType = ResponseDataType::OTHER;

    /**
     * @inheritdoc
     */
    public static function tidyResponse(array $response, Query $query)
    {
        $sections = $response[$query->getModule()]['section'];

        // Single section or multiple sections?
        // If single section: wrap it in an array to process it generically
        if (isset($sections['FL'])) {
            $sections = [$sections];
        }

        foreach ($sections as &$section) {
            if (! isset($section['FL'])) {
                continue;
            }

            $fields = $section['FL'];

            // Single field or multiple fields?
            // If single field: wrap it in an array to process it generically
            if (isset($fields['dv'])) {
                $section['FL'] = [$fields];
            }
        }

        return $sections;
    }
}
