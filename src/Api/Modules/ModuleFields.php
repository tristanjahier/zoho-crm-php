<?php

namespace Zoho\Crm\Api\Modules;

/**
 * Metamodule to query the fields of a given module.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/getfields.html
 */
class ModuleFields extends AbstractProxyModule
{
    /**
     * Get all the module's fields.
     *
     * The fields are grouped by section.
     *
     * @param array $params (optional) The URL parameters
     * @param callable $filter (optional) A callback function to filter the fields
     * @return array
     */
    public function getAll(array $params = [], callable $filter = null)
    {
        $sections = $this->newQuery('getFields', $params)->get();

        if (isset($filter)) {
            foreach($sections as &$section) {
                $section['FL'] = array_filter($section['FL'], $filter);

                if (empty($section['FL'])) {
                    unset($section['FL']);
                }
            }
        }

        return $sections;
    }

    /**
     * Get the module's native fields.
     *
     * @return array
     */
    public function getNative()
    {
        return $this->getAll([], function($field) {
            return $field['customfield'] === 'false';
        });
    }

    /**
     * Get the module's custom fields.
     *
     * @return array
     */
    public function getCustom()
    {
        return $this->getAll([], function($field) {
            return $field['customfield'] === 'true';
        });
    }

    /**
     * Get the module's summary fields.
     *
     * The summary is the section at the top of a Zoho record page.
     *
     * @return array
     */
    public function getSummary()
    {
        return $this->getAll(['type' => 1]);
    }

    /**
     * Get the module's mandatory fields.
     *
     * @return array
     */
    public function getMandatory()
    {
        return $this->getAll(['type' => 2]);
    }
}
