<?php

declare(strict_types=1);

namespace Zoho\Crm\V2\Records;

use Zoho\Crm\Support\Helper;
use Zoho\Crm\V2\AbstractSubApi;

/**
 * Helper for the Record APIs.
 *
 * @property ModuleHelper $leads
 * @property ModuleHelper $accounts
 * @property ModuleHelper $contacts
 * @property ModuleHelper $deals
 * @property ModuleHelper $campaigns
 * @property ModuleHelper $tasks
 * @property ModuleHelper $cases
 * @property ModuleHelper $events
 * @property ModuleHelper $calls
 * @property ModuleHelper $solutions
 * @property ModuleHelper $products
 * @property ModuleHelper $vendors
 * @property ModuleHelper $priceBooks
 * @property ModuleHelper $quotes
 * @property ModuleHelper $salesOrders
 * @property ModuleHelper $purchaseOrders
 * @property ModuleHelper $invoices
 * @property ModuleHelper $activities
 * @property ModuleHelper $notes
 */
class SubApi extends AbstractSubApi
{
    /**
     * Create a module helper.
     *
     * @param string $name The name of the module
     * @return ModuleHelper
     */
    public function module(string $name): ModuleHelper
    {
        return new ModuleHelper($this->client, $name);
    }

    /**
     * Get a module helper as a public property.
     *
     * The module name needs to be written in camel case.
     * Example: `$client->records->priceBooks` instead of `$client->records->module('PriceBooks')`.
     *
     * @param string $name The name of the module in camel case
     * @return ModuleHelper
     */
    public function __get(string $name)
    {
        return $this->module(Helper::inflector()->classify($name));
    }
}
