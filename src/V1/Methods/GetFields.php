<?php

namespace Zoho\Crm\V1\Methods;

use Zoho\Crm\V1\Query;
use Zoho\Crm\Entities\Collection;
use Zoho\Crm\V1\Entities\FieldSection;

/**
 * @see https://www.zoho.com/crm/developer/docs/api/getfields.html
 */
class GetFields extends AbstractMethod
{
    /**
     * @inheritdoc
     */
    public function isResponseEmpty(array $response, Query $query)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function cleanResponse(array $response, Query $query)
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

    /**
     * @inheritdoc
     */
    public function convertResponse($response, Query $query)
    {
        $sections = new Collection();

        foreach ($response as $section) {
            $sections->push(new FieldSection($section));
        }

        return $sections;
    }
}
