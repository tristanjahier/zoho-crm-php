<?php

namespace Zoho\Crm\Modules;

/**
 * Metamodule to query the fields of a given module.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/getfields.html
 */
class ModuleFields extends AbstractProxyModule
{
    /**
     * Create a query to get the module's field sections.
     *
     * Each section has its field collection.
     *
     * @param array $params (optional) The URL parameters
     * @return \Zoho\Crm\Query
     */
    public function sections(array $params = [])
    {
        return $this->newQuery('getFields', $params);
    }

    /**
     * Get the module's fields.
     *
     * @param array $params (optional) The URL parameters
     * @return \Zoho\Crm\Entities\Collection
     */
    public function getAll(array $params = [])
    {
        return $this->sections($params)->get()->pluck('FL')->collapse();
    }

    /**
     * Get the module's native fields.
     *
     * @return \Zoho\Crm\Entities\Collection
     */
    public function getNative()
    {
        return $this->getAll()->where('customfield', 'false');
    }

    /**
     * Get the module's custom fields.
     *
     * @return \Zoho\Crm\Entities\Collection
     */
    public function getCustom()
    {
        return $this->getAll()->where('customfield', 'true');
    }

    /**
     * Get the module's summary fields.
     *
     * The summary is the section at the top of a Zoho record page.
     *
     * @return \Zoho\Crm\Entities\Collection
     */
    public function getSummary()
    {
        return $this->getAll(['type' => 1]);
    }

    /**
     * Get the module's mandatory fields.
     *
     * @return \Zoho\Crm\Entities\Collection
     */
    public function getMandatory()
    {
        return $this->getAll(['type' => 2]);
    }
}
