<?php

namespace Zoho\Crm\Api\Methods;

use Zoho\Crm\Api\ResponseDataType;
use Zoho\Crm\Api\Request;

class GetFields extends AbstractMethod
{
    protected static $response_type = ResponseDataType::OTHER;

    public static function tidyResponse(array $response, Request $request)
    {
        $sections = $response[$request->getModule()]['section'];

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
