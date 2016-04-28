<?php

namespace Zoho\CRM\Core;

use Zoho\CRM\Entities\EntityCollection;
use Zoho\CRM\Entities\AbstractEntity;

class XmlBuilder
{
    public static function buildRecords($module, $records)
    {
        // If $records is an Entity or an EntityCollection, convert it to an array
        if ($records instanceof AbstractEntity)
            $records = [$records->getData()];
        elseif ($records instanceof EntityCollection)
            $records = $records->toRawArray();

        $xml = new \SimpleXMLElement("<$module/>");

        $row_count = 1;

        foreach ($records as $record) {
            $row = $xml->addChild('row');
            $row->addAttribute('no', $row_count);

            foreach ($record as $attr_name => $attr_value) {
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
