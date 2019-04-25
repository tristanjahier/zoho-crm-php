<?php

namespace Zoho\Crm\Exceptions;

use Exception;

class UnsupportedEntityPropertyException extends Exception
{
    /**
     * The constructor.
     *
     * @param string $entity The name of the entity
     * @param string $property The name of the property
     */
    public function __construct($entity, $property)
    {
        parent::__construct("Entity $entity does not have a $property property.");
    }
}
