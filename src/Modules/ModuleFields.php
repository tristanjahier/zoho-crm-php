<?php

namespace Zoho\CRM\Modules;

class ModuleFields extends AbstractProxyModule
{
    public function getAll(array $params = [], callable $filter = null)
    {
        $sections = $this->request('getFields', $params);

        if (isset($filter)) {
            foreach($sections as &$section) {
                $section['FL'] = array_filter($section['FL'], $filter);
                if (empty($section['FL']))
                    unset($section['FL']);
            }
        }

        return $sections;
    }

    public function getNative()
    {
        return $this->getAll([], function($field) {
            return $field['customfield'] === 'false';
        });
    }

    public function getCustom()
    {
        return $this->getAll([], function($field) {
            return $field['customfield'] === 'true';
        });
    }

    public function getSummary()
    {
        return $this->getAll(['type' => 1]);
    }

    public function getMandatory()
    {
        return $this->getAll(['type' => 2]);
    }
}
