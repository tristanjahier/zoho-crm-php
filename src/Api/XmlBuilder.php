<?php

namespace Zoho\Crm\Api;

use SimpleXMLElement;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\Entities\Collection;
use Zoho\Crm\Entities\AbstractEntity;

class XmlBuilder
{
    public static function buildRecords($module, $records)
    {
        if ($records instanceof Collection) {
            $records = $records->toArray();
        }

        $xml = new SimpleXMLElement("<$module/>");

        $row_count = 1;

        foreach ($records as $record) {
            $row = $xml->addChild('row');
            $row->addAttribute('no', $row_count);

            if ($record instanceof AbstractEntity) {
                $record = $record->toArray();
            }

            foreach ($record as $attr_name => $attr_value) {
                // Stringify boolean values
                if (is_bool($attr_value)) {
                    $attr_value = Helper::booleanToString($attr_value);
                }

                $attr = $row->addChild('FL', $attr_value);
                $attr->addAttribute('val', $attr_name);
            }

            $row_count++;
        }

        // We need to return the XML as a string,
        // but also to get rid of the XML version declaration node.
        // Otherwise Zoho won't be able to parse it...
        $document = dom_import_simplexml($xml);
        return $document->ownerDocument->saveXML($document->ownerDocument->documentElement);
    }
}
