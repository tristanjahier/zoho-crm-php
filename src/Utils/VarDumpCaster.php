<?php

namespace Zoho\Crm\Utils;

use Symfony\Component\VarDumper\Caster\Caster;
use Symfony\Component\VarDumper\Caster\CutStub;
use Doctrine\Common\Inflector\Inflector;
use Zoho\Crm\Client;
use Zoho\Crm\Api\Modules\AbstractModule;
use Zoho\Crm\Api\Query;
use Zoho\Crm\Entities\Entity;
use Zoho\Crm\Support\Collection;

/**
 * Caster for Symfony's var-dumper.
 *
 * Useful in development to provide a nice and informative output while dumping
 * the most complex and important objects of this library.
 *
 * @see https://symfony.com/doc/current/components/var_dumper/advanced.html#casters
 */
class VarDumpCaster
{
    /**
     * Get an associative array of the custom casters.
     *
     * @see \Symfony\Component\VarDumper\Cloner\AbstractCloner::addCasters()
     *
     * @return array
     */
    public static function getConfig()
    {
        return [
            Client::class => self::class.'::castClient',
            AbstractModule::class => self::class.'::castModule',
            Query::class => self::class.'::castQuery',
            Entity::class => self::class.'::castEntity',
            Collection::class => self::class.'::castCollection',
        ];
    }

    /**
     * Cast a client instance.
     *
     * @param \Zoho\Crm\Client $client The client instance
     * @return array
     */
    public static function castClient(Client $client)
    {
        $result = [
            Caster::PREFIX_PROTECTED . 'endpoint' => $client->getEndpoint(),
            Caster::PREFIX_PROTECTED . 'requestCount' => $client->getRequestCount(),
        ];

        $modules = array_merge($client->modules(), $client->aliasedModules());

        foreach ($modules as $name => $instance) {
            $result[Inflector::camelize($name)] = new CutStub($instance);
        }

        return $result;
    }

    /**
     * Cast a module handler instance.
     *
     * @param \Zoho\Crm\Api\Modules\AbstractModule $module The module instance
     * @return array
     */
    public static function castModule(AbstractModule $module)
    {
        return [
            Caster::PREFIX_PROTECTED . 'client' => new CutStub($module->client()),
            Caster::PREFIX_PROTECTED . 'name' => $module->name(),
            Caster::PREFIX_PROTECTED . 'supportedMethods' => $module->supportedMethods(),
        ];
    }

    /**
     * Cast a query instance.
     *
     * @param \Zoho\Crm\Api\Query $query The query instance
     * @return array
     */
    public static function castQuery(Query $query)
    {
        $result = (array) $query;

        $result[Caster::PREFIX_PROTECTED . 'client'] = new CutStub($query->getClient());

        return $result;
    }

    /**
     * Cast an entity instance.
     *
     * @param \Zoho\Crm\Entities\Entity $entity The entity instance
     * @return array
     */
    public static function castEntity(Entity $entity)
    {
        return self::prefixKeys($entity->toArray(), Caster::PREFIX_VIRTUAL);
    }

    /**
     * Cast a collection instance.
     *
     * @param \Zoho\Crm\Support\Collection $collection The collection instance
     * @return array
     */
    public static function castCollection(Collection $collection)
    {
        return [
            Caster::PREFIX_PROTECTED . 'items' => $collection->items()
        ];
    }

    /**
     * Prefix the keys of an array with a given string.
     *
     * @param array $array The array
     * @param string $prefix The key prefix
     * @return array
     */
    private static function prefixKeys(array $array, string $prefix)
    {
        $result = [];

        foreach ($array as $key => $value) {
            $result[$prefix . $key] = $value;
        }

        return $result;
    }
}
