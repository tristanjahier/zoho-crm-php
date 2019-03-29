<?php

namespace Zoho\Crm\Utils;

use Symfony\Component\VarDumper\Caster\Caster;
use Symfony\Component\VarDumper\Caster\CutStub;
use Doctrine\Common\Inflector\Inflector;
use Zoho\Crm\Client;
use Zoho\Crm\Api\Modules\AbstractModule;
use Zoho\Crm\Api\Query;
use Zoho\Crm\Entities\AbstractEntity;
use Zoho\Crm\Support\Collection;

class VarDumpCaster
{
    public static function getConfig()
    {
        return [
            Client::class => self::class.'::castClient',
            AbstractModule::class => self::class.'::castModule',
            Query::class => self::class.'::castQuery',
            AbstractEntity::class => self::class.'::castEntity',
            Collection::class => self::class.'::castCollection',
        ];
    }

    public static function castClient(Client $client)
    {
        $result = [
            Caster::PREFIX_PROTECTED . 'endpoint' => $client->getEndpoint(),
            Caster::PREFIX_PROTECTED . 'request_count' => $client->getRequestCount(),
        ];

        foreach ($client->modules() as $name => $instance) {
            $result[Inflector::camelize($name)] = new CutStub($instance);
        }

        return $result;
    }

    public static function castModule(AbstractModule $module)
    {
        return [
            Caster::PREFIX_PROTECTED . 'client' => new CutStub($module->client()),
            Caster::PREFIX_PROTECTED . 'name' => $module->name(),
            Caster::PREFIX_PROTECTED . 'supported_methods' => $module->supportedMethods(),
        ];
    }

    public static function castQuery(Query $query)
    {
        $result = (array) $query;

        $result[Caster::PREFIX_PROTECTED . 'client'] = new CutStub($query->getClient());

        return $result;
    }

    public static function castEntity(AbstractEntity $entity)
    {
        return self::prefixKeys($entity->toArray(), Caster::PREFIX_VIRTUAL);
    }

    public static function castCollection(Collection $collection)
    {
        return [
            Caster::PREFIX_PROTECTED . 'items' => $collection->items()
        ];
    }

    private static function prefixKeys(array $array, string $prefix)
    {
        $result = [];

        foreach ($array as $key => $value) {
            $result[$prefix . $key] = $value;
        }

        return $result;
    }
}
