<?php

namespace Zoho\Crm\Api;

use DateTime;
use InvalidArgumentException;
use Zoho\Crm\Client;
use Zoho\Crm\Api\UrlParameters;
use Zoho\Crm\Exceptions\InvalidQueryException;
use Zoho\Crm\Entities\Entity;
use Zoho\Crm\Entities\Collection;
use Zoho\Crm\Support\Helper;

/**
 * A container for all the attributes of an API request.
 *
 * It contains the format, the module, the method and the URL parameters.
 * It provides a fluent interface to set the different attributes of an API request.
 */
class Query
{
    /** @var \Zoho\Crm\Client The API client that originated this query */
    protected $client;

    /** @var string The response format */
    protected $format;

    /** @var string The name of the Zoho module */
    protected $module;

    /** @var string The API method */
    protected $method;

    /** @var UrlParameters The URL parameters collection */
    protected $parameters;

    /** @var bool Whether the query must be paginated or not */
    protected $paginated = false;

    /** @var int|null The maximum number of concurrent requests allowed to fetch pages */
    protected $concurrency;

    /** @var int|null The maximum number of records to fetch */
    protected $limit;

    /** @var \DateTime The maximum modification date to fetch records */
    protected $maxModificationDate;

    /**
     * The constructor.
     *
     * @param \Zoho\Crm\Client $client The client to use to make the request
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->parameters = new UrlParameters;
    }

    /**
     * Get the bound API client.
     *
     * @return \Zoho\Crm\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Get the response format.
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set the response format.
     *
     * @param string $format The desired response format
     * @return $this
     */
    public function format(string $format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get the requested module.
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set the requested module.
     *
     * @param string $module The module name
     * @return $this
     */
    public function module(string $module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get the requested API method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set the requested API method.
     *
     * @param string $method The method name
     * @return $this
     */
    public function method(string $method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get the URL parameters.
     *
     * @return UrlParameters
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get the value of a URL parameter by key.
     *
     * @param string $key The parameter key
     * @return mixed
     */
    public function getParameter(string $key)
    {
        return $this->parameters[$key];
    }

    /**
     * Check if a URL parameter exists by key.
     *
     * @param string $key The parameter key
     * @return bool
     */
    public function hasParameter(string $key)
    {
        return $this->parameters->has($key);
    }

    /**
     * Remove all URL parameters.
     *
     * If an argument is passed, they will be replaced by a new set.
     *
     * @param array|UrlParameters $parameters (optional) The new set of parameters
     * @return $this
     */
    public function resetParameters($parameters = [])
    {
        if (! $parameters instanceof UrlParameters) {
            $parameters = new UrlParameters($parameters);
        }

        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Set a URL parameter.
     *
     * @param string $key The key
     * @param mixed $value (optional) The value
     * @return $this
     */
    public function param(string $key, $value = null)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * Set multiple URL parameters.
     *
     * @param array $parameters The parameters
     * @return $this
     */
    public function params(array $parameters)
    {
        foreach ($parameters as $key => $value) {
            $this->param($key, $value);
        }

        return $this;
    }

    /**
     * Remove a URL parameter by key.
     *
     * @param string $key The parameter key
     * @return $this
     */
    public function removeParam(string $key)
    {
        $this->parameters->unset($key);

        return $this;
    }

    /**
     * Order records by a given column, in a given direction.
     *
     * The ordering direction must be either 'asc' or 'desc'.
     *
     * @param string $column The column name
     * @param string $order (optional) The ordering direction
     * @return $this
     */
    public function orderBy(string $column, string $order = 'asc')
    {
        return $this->params([
            'sortColumnString' => $column,
            'sortOrderString' => $order
        ]);
    }

    /**
     * Order records by ascending order.
     *
     * @return $this
     */
    public function orderAsc()
    {
        return $this->param('sortOrderString', 'asc');
    }

    /**
     * Order records by descending order.
     *
     * @return $this
     */
    public function orderDesc()
    {
        return $this->param('sortOrderString', 'desc');
    }

    /**
     * Select one or more fields/columns to retrieve.
     *
     * @param string[] $columns An array of column names
     * @return $this
     */
    public function select($columns)
    {
        $columns = is_array($columns) ? $columns : func_get_args();

        $currentSelection = $this->getSelectedColumns();
        $newSelection = array_unique(array_merge($currentSelection, $columns));

        return $this->param('selectColumns', $this->wrapSelectedColumns($newSelection));
    }

    /**
     * Unselect one or more fields/columns.
     *
     * @param string[] $columns An array of column names
     * @return $this
     */
    public function unselect($columns)
    {
        $columns = is_array($columns) ? $columns : func_get_args();

        $currentSelection = $this->getSelectedColumns();
        $newSelection = array_diff($currentSelection, $columns);

        return $this->param('selectColumns', $this->wrapSelectedColumns($newSelection));
    }

    /**
     * Wrap an array of field/column names into a valid "selectColumns" parameter value.
     *
     * @param string[] $columns An array of column names
     * @return string
     */
    protected function wrapSelectedColumns(array $columns)
    {
        return $this->module . '(' . implode(',', $columns) . ')';
    }

    /**
     * Get the selected fields/columns.
     *
     * @return string[]
     */
    public function getSelectedColumns()
    {
        $selection = $this->parameters->get('selectColumns');

        if ($selection === null) {
            return [];
        }

        // Unwrap the column names
        $selection = substr($selection, strlen($this->module.'('), -1);

        // Split the string on coma and trim the column names
        return array_map('trim', array_filter(explode(',', $selection)));
    }

    /**
     * Check if a field/column is selected.
     *
     * @param string $column The column to check
     * @return bool
     */
    public function hasSelect(string $column)
    {
        return in_array($column, $this->getSelectedColumns());
    }

    /**
     * Remove selection of fields/columns.
     *
     * @return $this
     */
    public function selectAll()
    {
        return $this->removeParam('selectColumns');
    }

    /**
     * Select the creation and last modification timestamps.
     *
     * @return $this
     */
    public function selectTimestamps()
    {
        return $this->select('Created Time', 'Modified Time');
    }

    /**
     * Select a set of default fields which are present on all records.
     *
     * @return $this
     */
    public function selectDefaultColumns()
    {
        $entityName = $this->getClientModule()->newEntity()::name();

        return $this->select("$entityName Owner", 'Created By', 'Modified By')->selectTimestamps();
    }

    /**
     * Set the minimum date for records' last modification.
     *
     * @param \DateTime|string $date A date object or a valid string
     * @return $this
     */
    public function modifiedAfter($date)
    {
        if ($date === null) {
            $this->parameters->unset('lastModifiedTime');
            return $this;
        }

        if (! ($date instanceof DateTime) && is_string($date)) {
            $date = new DateTime($date);
        }

        return $this->param('lastModifiedTime', $date);
    }

    /**
     * Set the maximum date for records' last modification.
     *
     * @param \DateTime|string $date A date object or a valid string
     * @return $this
     */
    public function modifiedBefore($date)
    {
        if (! ($date instanceof DateTime) && is_string($date)) {
            $date = new DateTime($date);
        }

        $this->maxModificationDate = $date;

        return $this;
    }

    /**
     * Get the maximum date for records' last modification.
     *
     * @return \DateTime
     */
    public function getMaxModificationDate()
    {
        return $this->maxModificationDate;
    }

    /**
     * Check if the query has a maximum modification date for records.
     *
     * @return bool
     */
    public function hasMaxModificationDate()
    {
        return isset($this->maxModificationDate);
    }

    /**
     * Set the minimum and maximum dates for records' last modification.
     *
     * @param \DateTime|string $from A date object or a valid string
     * @param \DateTime|string $to A date object or a valid string
     * @return $this
     */
    public function modifiedBetween($from, $to)
    {
        return $this->modifiedAfter($from)->modifiedBefore($to);
    }

    /**
     * Trigger the workflow rules on Zoho after the query execution.
     *
     * @param bool $enabled (optional) Whether the workflow rules should be triggered
     * @return $this
     */
    public function triggerWorkflowRules(bool $enabled = true)
    {
        return $this->param('wfTrigger', Helper::booleanToString($enabled));
    }

    /**
     * Limit the number of records to retrieve.
     *
     * @param int|null $limit The number of records
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function limit(?int $limit)
    {
        if (! is_null($limit) && (! is_int($limit) || $limit <= 0)) {
            throw new InvalidArgumentException('Query limit must be a positive non-zero integer.');
        }

        $this->limit = $limit;

        return $this;
    }

    /**
     * Get the limit of records to retrieve.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Check if there is a limit of records to retrieve.
     *
     * @return bool
     */
    public function hasLimit()
    {
        return isset($this->limit);
    }

    /**
     * Turn pagination on/off for the query.
     *
     * If enabled, the pages will be automatically fetched on query execution.
     *
     * @param bool $paginated (optional) Whether the query is paginated
     * @return $this
     */
    public function paginated(bool $paginated = true)
    {
        $this->paginated = $paginated;

        if (! $paginated) {
            $this->concurrency(null);
        }

        return $this;
    }

    /**
     * Check if the query is paginated.
     *
     * @return bool
     */
    public function isPaginated()
    {
        return $this->paginated;
    }

    /**
     * Create a paginator for the query.
     *
     * @return QueryPaginator
     */
    public function getPaginator()
    {
        return new QueryPaginator($this);
    }

    /**
     * Set the concurrency limit for asynchronous pagination.
     *
     * @param int|null $concurrency The concurrency limit
     * @return $this
     */
    public function concurrency(?int $concurrency)
    {
        if (! is_null($concurrency) && (! is_int($concurrency) || $concurrency <= 0)) {
            throw new InvalidArgumentException('Query concurrency must be a positive non-zero integer.');
        }

        $this->concurrency = $concurrency;

        return $this;
    }

    /**
     * Get the concurrency limit for asynchronous pagination.
     *
     * @return int|null
     */
    public function getConcurrency()
    {
        return $this->concurrency;
    }

    /**
     * Determine if the query requires asynchronous pagination.
     *
     * @return bool
     */
    public function mustFetchPagesAsynchronously()
    {
        return isset($this->concurrency) && $this->concurrency > 1;
    }

    /**
     * Build the query URI.
     *
     * @return string
     */
    public function buildUri()
    {
        return "{$this->format}/{$this->module}/{$this->method}?{$this->parameters}";
    }

    /**
     * Check if the query is malformed.
     *
     * @return bool
     */
    public function isMalformed()
    {
        return is_null($this->format) || is_null($this->module) || is_null($this->method);
    }

    /**
     * Validate the query.
     *
     * Check attributes consistency and the presence of the required ones.
     * If the validation passes, nothing will happen.
     * If it fails, an exception will be thrown.
     *
     * @return void
     *
     * @throws \Zoho\Crm\Exceptions\InvalidQueryException
     */
    public function validate()
    {
        // Very basic validation: just check that required parts are present.
        if ($this->isMalformed()) {
            throw new InvalidQueryException($this, 'malformed URI.');
        }

        // "Modified Time" column has to be be present in the results
        // for "modifiedBefore()" constraint to work properly.
        $selectedColumns = $this->getSelectedColumns();
        $modifiedDateIsMissing = ! empty($selectedColumns) && ! in_array('Modified Time', $selectedColumns);

        if ($this->hasMaxModificationDate() && $modifiedDateIsMissing) {
            $message = '"Modified Time" column is required with "modifiedBefore()" constraint.';
            throw new InvalidQueryException($this, $message);
        }

        if ($this->mustFetchPagesAsynchronously() && ! $this->isPaginated()) {
            throw new InvalidQueryException($this, 'setting concurrency on a non-paginated query is irrelevant.');
        }
    }

    /**
     * Check if the query passes validation.
     *
     * @return bool
     */
    public function isValid()
    {
        try {
            $this->validate();
        } catch (InvalidQueryException $e) {
            return false;
        }

        return true;
    }

    /**
     * Execute the query with the bound client.
     *
     * @return Response
     */
    public function execute()
    {
        return $this->client->executeQuery($this);
    }

    /**
     * Execute the query and get a result adapted to its nature.
     *
     * @return mixed
     */
    public function get()
    {
        return $this->execute()->getContent();
    }

    /**
     * Retrieve only the first record matched by the query.
     *
     * It will fail if called on a query which is not supposed to retrieve records.
     *
     * @return \Zoho\Crm\Entities\Entity
     */
    public function first()
    {
        // Set the range of fetched records to 1 to optimize the execution time.

        return $this->copy()
            ->param('toIndex', $this->getParameter('fromIndex'))
            ->paginated(false)
            ->get()
            ->first();
    }

    /**
     * Get the module instance attached to the bound client.
     *
     * @return Modules\AbstractModule
     */
    public function getClientModule()
    {
        return $this->client->module($this->module);
    }

    /**
     * Get the method handler attached to the bound client.
     *
     * @return Methods\AbstractMethod
     */
    public function getClientMethod()
    {
        return $this->client->getMethodHandler($this->method);
    }

    /**
     * Create a deep copy of the query.
     *
     * @return static
     */
    public function copy()
    {
        return clone $this;
    }

    /**
     * Allow the deep cloning of the query.
     *
     * @return void
     */
    public function __clone()
    {
        $this->parameters = clone $this->parameters;
    }
}
