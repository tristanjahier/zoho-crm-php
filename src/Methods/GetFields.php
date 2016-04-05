<?php

namespace Zoho\CRM\Methods;

class GetFields extends AbstractMethod
{
    public static function tidyResponse(array $response, $module)
    {
        $entries = [];

        foreach ($response[$module]['section'] as $section) {
            $section_entry = $section;
            $section_entry['fields'] = $section_entry['FL'];
            unset($section_entry['FL']);
            $entries[] = $section_entry;
        }

        return $entries;
    }
}
