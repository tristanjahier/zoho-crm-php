<?php

namespace Zoho\CRM\Api\Methods;

use Zoho\CRM\Api\ResponseDataType;
use Zoho\CRM\Api\Request;

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

        return $sections;
    }
}
