<?php

namespace Zoho\CRM\Methods;

use Zoho\CRM\Core\ApiResponseType;
use Zoho\CRM\Core\Request;

class GetFields extends AbstractMethod
{
    protected static $response_type = ApiResponseType::OTHER;

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
