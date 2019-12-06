<?php

namespace Zoho\Crm\Utils;

use Symfony\Component\VarDumper\Caster\Caster;
use Symfony\Component\VarDumper\Caster\CutStub;
use Doctrine\Common\Inflector\Inflector;
use Zoho\Crm\Contracts\ClientInterface;
use Zoho\Crm\Contracts\QueryInterface;
use Zoho\Crm\Entities\Entity;
use Zoho\Crm\Support\Collection;
use Zoho\Crm\Support\UrlParameters;
use Zoho\Crm\V1\Client as V1Client;
use Zoho\Crm\V1\Modules\AbstractModule;
use Zoho\Crm\V2\Client as V2Client;

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
            ClientInterface::class => self::class.'::castClient',
            AbstractModule::class => self::class.'::castModule',
            QueryInterface::class => self::class.'::castQuery',
            Entity::class => self::class.'::castEntity',
            Collection::class => self::class.'::castCollection',
        ];
    }

    /**
     * Cast a client instance.
     *
     * @param \Zoho\Crm\Contracts\ClientInterface $client The client instance
     * @return array
     */
    public static function castClient(ClientInterface $client)
    {
        $properties = [
            Caster::PREFIX_PROTECTED . 'endpoint' => $client->getEndpoint(),
            Caster::PREFIX_PROTECTED . 'requestCount' => $client->getRequestCount(),
        ];

        if ($client instanceof V1Client) {
            $properties = array_merge($properties, self::castV1Client($client));
        } elseif ($client instanceof V2Client) {
            $properties = array_merge($properties, self::castV2Client($client));
        }

        return $properties;
    }

    /**
     * Cast a V1 client instance.
     *
     * @param \Zoho\Crm\V1\Client $client The client instance
     * @return array
     */
    public static function castV1Client(V1Client $client)
    {
        $modules = array_merge($client->modules(), $client->aliasedModules());
        $properties = [];

        foreach ($modules as $name => $instance) {
            $properties[Inflector::camelize($name)] = new CutStub($instance);
        }

        return $properties;
    }

    /**
     * Cast a module handler instance.
     *
     * @param \Zoho\Crm\V1\Modules\AbstractModule $module The module instance
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
     * Cast a V2 client instance.
     *
     * @param \Zoho\Crm\V2\Client $client The client instance
     * @return array
     */
    public static function castV2Client(V2Client $client)
    {
        $properties = [];

        foreach ($client->getSubApis() as $name => $instance) {
            $properties[$name] = new CutStub($instance);
        }

        return $properties;
    }

    /**
     * Cast a query instance.
     *
     * @param \Zoho\Crm\Contracts\QueryInterface $query The query instance
     * @return array
     */
    public static function castQuery(QueryInterface $query)
    {
        $uriComponents = parse_url($query->getUri());
        $uriComponents['query'] = UrlParameters::createFromString($uriComponents['query'] ?? '')->toArray();

        return self::prefixKeys([
            'httpMethod' => $query->getHttpMethod(),
            'uri' => $uriComponents,
            'headers' => $query->getHeaders(),
            'body' => mb_strimwidth((string) $query->getBody(), 0, 128, ' … (truncated)', 'UTF-8')
        ], Caster::PREFIX_PROTECTED);
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
