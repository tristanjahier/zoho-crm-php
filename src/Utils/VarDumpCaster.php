<?php

namespace Zoho\Crm\Utils;

use Symfony\Component\VarDumper\Caster\Caster;
use Symfony\Component\VarDumper\Caster\CutStub;
use Zoho\Crm\Contracts\ClientInterface;
use Zoho\Crm\Contracts\RequestInterface;
use Zoho\Crm\Entities\Entity;
use Zoho\Crm\Support\Helper;
use Zoho\Crm\Support\Collection;
use Zoho\Crm\Support\UrlParameters;
use Zoho\Crm\V2\Client as V2Client;

/**
 * Caster for Symfony's var-dumper.
 *
 * Useful in development to provide a nice and informative output while dumping
 * the most complex and important objects of this library.
 *
 * @see https://symfony.com/doc/current/components/var_dumper.html#casters
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
            RequestInterface::class => self::class.'::castRequest',
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

        if ($client instanceof V2Client) {
            $properties = array_merge($properties, self::castV2Client($client));
        }

        return $properties;
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
     * Cast a request instance.
     *
     * @param \Zoho\Crm\Contracts\RequestInterface $request The request instance
     * @return array
     */
    public static function castRequest(RequestInterface $request)
    {
        $urlComponents = parse_url($request->getUrl());
        $urlComponents['query'] = UrlParameters::createFromString($urlComponents['query'] ?? '')->toArray();

        return self::prefixKeys([
            'httpMethod' => $request->getHttpMethod(),
            'url' => $urlComponents,
            'headers' => $request->getHeaders(),
            'body' => mb_strimwidth((string) $request->getBody(), 0, 128, ' … (truncated)', 'UTF-8')
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
