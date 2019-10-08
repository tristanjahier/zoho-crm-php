# Zoho CRM API wrapper library (PHP)

This is an API wrapper library for Zoho CRM, written in PHP.

It aims to cover the whole API (every module and method), while providing a great abstraction and very easy-to-use methods.

## Requirements

- PHP : `7.3+`
- PHP cURL extension enabled

## Installation

The recommended way to install this package is through [Composer](https://getcomposer.org).

Edit your `composer.json` file:

```json
"require": {
    "tristanjahier/zoho-crm-php": "^0.3"
}
```

or simply run this command:

```
composer require tristanjahier/zoho-crm-php
```

## Getting started

### TL;DR - A quick example

Here are just a few examples of what is possible to do with this library:

```php
// Create an API client
$client = new Zoho\Crm\Client('MY_ZOHO_AUTH_TOKEN');

// Create a query and execute it
$response = $client->newQuery('Calls', 'getRecords', ['fromIndex' => 201, 'toIndex' => 400])->execute();

// Retrieve all potentials modified for the last time after April 1st, 2019
$potentials = $client->potentials->all()->modifiedAfter('2019-04-01')->get();

// Retrieve records by ID
$oneLead = $client->leads->find('1212717324723478324');
$manyLeads = $client->leads->findMany(['8734873457834574028', '3274736297894375750']);

// Create a new contact
$contactId = $client->contacts->insert([
    'First Name' => 'Jean',
    'Last Name' => 'Dupont',
    'Email' => 'jacques@dupont.fr'
]);

// Update the name of the contact
$client->contacts->update($contactId, ['First Name' => 'Jacques']);

// Delete this contact
$client->contacts->delete($contactId);
```

### The basics

The main component of this library is the `Client` class. This is the **starting point** for each API request.

To create an API client you simply need a Zoho auth token:
```php
$client = new Zoho\Crm\Client('MY_ZOHO_AUTH_TOKEN');
```

You are now ready to make API requests! First, you need to create a query. This is done through the `newQuery()` method. It takes up to 4 arguments (all optional):
1. `$module`: the module name
2. `$method`: the API method name
3. `$params`: an array of URL parameters
4. `$paginated`: a boolean to enable/disable query pagination

A few examples:
```php
// Retrieve all records from the Contacts module, modified after April 1st, 2019:
$query = $client->newQuery('Contacts', 'getRecords', ['lastModifiedTime' => '2019-04-01'], true);

// Retrieve a Potentials record by ID:
$query = $client->newQuery('Potentials', 'getRecordById', ['id' => 'zoho record ID']);
```

Creating a query does not make any request to the API, you need to execute it:
```php
$response = $query->execute();
```

If the request is successful, it returns a `Response` instance. The API response is parsed and cleaned up for you, you simply have to use `getContent()` to get your data:

```php
$data = $response->getContent();
```

The type of the content depends on the request executed (method, parameters etc.).

All summarized:
```php
$response = $client->newQuery('Potentials', 'getRecordById', ['id' => 'zoho record ID'])->execute();
$records = $response->getContent();
```

*But... that's a bit verbose right?* Yes. This is just the most basic way to make an API request. Read the next sections to learn how to make better use of the library.

### The query object

A query is an instance of the class `Zoho\Crm\Api\Query`. It is simply a container for all the parameters that define a Zoho API request.

Remember that all the arguments of the `Client::newQuery()` method are optional. You can create an empty query:
```php
$query = $client->newQuery();
```

Then you can alter it using its fluent interface:

```php
$query->module('Contacts')
    ->method('getRecords')
    ->param('lastModifiedTime', '2019-04-01')
    ->param('sortColumnString', 'Modified Time')
    ->param('sortOrderString', 'desc')
    ->paginated();
```

The `Query` class has many methods that provide a great abstraction and help you write concise and readable code. For example, the above query could be rewritten like this:

```php
$query = $client->newQuery('Contacts', 'getRecords')
    ->modifiedAfter('2019-04-01')
    ->orderBy('Modified Time', 'desc')
    ->paginated();
```

Look at the code to find out all possibilities.

If you do not want to bother with the formal `Response` object, you can simply call `Query::get()`. It will execute the query and return the response content:

```php
$data = $query->get();
// is strictly equivalent to
$data = $query->execute()->getContent();
```

### Response types

The data type of a response depends on the method you call. It can be a scalar like a string, an array, a boolean or null. But in most cases, you will get either an entity or a collection of entities.

Entities are objects containing a set of coherent data. For example, a Zoho record (contact, call, lead etc.) is an entity.

When the response contains (or should contain) multiple entities, you get an *entity collection*.

```php
// Returns a collection of entities:
$client->newQuery('Calls', 'getRecords')->modifiedAfter('2019-04-01')->get();

// Returns a single entity of type Call:
$client->newQuery('Calls', 'getRecordById', ['id' => 'record ID'])->get();
```

#### Entities

An entity is an instance of `Zoho\Crm\Entities\Entity` (or any subclass).

It encapsulates the attributes of common API objects like records or users for example.

It provides a few useful methods:
- `has($attribute)`: check if an attribute is defined
- `get($attribute)`: get the value of an attribute
- `set($attribute, $value)`: set the value of an attribute
- `getId()`: get the entity ID
- `toArray()`: get the raw attributes array

It implements magic methods `__get()` and `__set()` which lets you manipulate its attributes like public properties:

```php
$id = $contact->CONTACTID;
$contact->Phone = '+1234567890';
```

#### Entity collections

An entity collection is an instance of `Zoho\Crm\Entities\Collection`.

A collection is an array wrapper which provide a fluent interface to manipulate its items. In the case of an entity collection, these items are entities.

It provides a bunch of useful methods. To name a few:
- `has($key)`: determine if an item exists at a given index
- `get($key, $default = null)`: get the item at a given index
- `count()`: get the number of items in the collection
- `isEmpty()`: determine if the collection is empty
- `first(callable $callback = null, $default = null)`: get the first item in the collection
- `firstWhere($key, $operator, $value = null)`: get the first item matching the given (key, [operator,] value) tuple
- `last(callable $callback = null, $default = null)`: get the last item in the collection
- `lastWhere($key, $operator, $value = null)`: get the last item matching the given (key, [operator,] value) tuple
- `map(callable $callback)`: apply a callback over each item and return a new collection with the results
- `sum($property = null)`: compute the sum of the items
- `filter(callable $callback = null)`: filter the collection items with a callback
- `where($key, $operator, $value = null)`: filter items based on a comparison tuple: (key, [operator,] value)
- `pluck($value, $key = null)`: get the values of a given item property by key

Look at the code of `Zoho\Crm\Support\Collection` for more details.

It implements `ArrayAccess` and `IteratorAggregate` which lets you manipulate it like an array:

```php
// If $records is an instance of Zoho\Crm\Entities\Collection...

// You can access items with square brackets:
$aRecord = $records[2];
$records[] = new Zoho\Crm\Entities\Entity();

// And you can loop through it:
foreach ($records as $record) {
    ...
}
```

### The module handlers

The client comes with module handlers attached to it, which extend `Zoho\Crm\Api\Modules\AbstractModule`.

They are accessible either with the `Client::module()` method or by calling the name of the module as a public property (in camel case).

```php
$client->module('Potentials');
// is equivalent to
$client->potentials;
```

A module handler also has a `newQuery()` method, which is the same as `Client::newQuery()` without the first `$module` argument:

```php
$client->potentials->newQuery('getMyRecords')
    ->modifiedAfter('2019-04-01')
    ->orderBy('Modified Time', 'desc')
    ->paginated();
```

In addition, most of the modules have methods to help you write even more shorter and cleaner queries. They will be referenced in the next part of this guide.

## Modules helpers reference

### Records modules

- Calls
- Contacts
- Events
- Leads
- Potentials
- Products
- Tasks
- Vendors

#### `all()`

Paginated query on method `getRecords`.

```php
$client->potentials->all();
// is equivalent to
$client->potentials->newQuery('getRecords', [], true);
```

#### `mine()`

Paginated query on method `getMyRecords`.

```php
$client->potentials->mine()
// is equivalent to
$client->potentials->newQuery('getMyRecords', [], true);
```

#### `search($criteria)`

Paginated query on method `searchRecords`.

```php
$client->potentials->search('Key:Value');
// is equivalent to
$client->potentials->newQuery('searchRecords', ['criteria' => "(Key:Value)"], true);
```

#### `searchBy($key, $value)`

Paginated query on method `searchRecords`.

```php
$client->potentials->searchBy('Key', 'Value');
// is equivalent to
$client->potentials->newQuery('searchRecords', ['criteria' => "(Key:Value)"], true);
```

#### `relatedTo($module, $id)`

Paginated query on method `getRelatedRecords`.

```php
$client->potentials->relatedTo('Contacts', 'Contact ID');
// is equivalent to
$client->potentials->newQuery('getRelatedRecords', [
    'parentModule' => 'Contacts',
    'id' => 'Contact ID'
], true);
```

#### `searchByPredefinedColumn($column, $value)`

Paginated query on method `getSearchRecordsByPDC`.

```php
$client->potentials->searchByPredefinedColumn('Column', 'Value');
// is equivalent to
$client->potentials->newQuery('getSearchRecordsByPDC', [
    'searchColumn' => 'Column',
    'searchValue' => 'Value'
], true);
```

#### `deletedIds()`

Paginated query on method `getDeletedRecordIds`.

```php
$client->potentials->deletedIds();
// is equivalent to
$client->potentials->newQuery('getDeletedRecordIds', [], true);
```

---

On top of that, there are a bunch of method that create queries, execute them and return the result:

#### `find($id)`

Retrieve a record by its ID.

```php
$record = $client->calls->find('Record ID');
```

#### `findMany($ids)`

Retrieve multiple records by their IDs.

```php
$records = $client->calls->findMany(['Record 1 ID', 'Record 2 ID']);
```

#### `insert($data)`

Insert a new record.

```php
$client->calls->insert([
    'Field 1' => 'Value 1',
    'Field 2' => 'Value 2',
    ...
]);
```

#### `insertMany($data)`

Insert multiple new records.

```php
$records = [
    [
        'Field 1' => 'Value 1',
        'Field 2' => 'Value 2',
        ...
    ], [
        'Field 1' => 'Value 1',
        'Field 2' => 'Value 2',
        ...
    ],
    ...
];

$client->calls->insertMany($records);
```

#### `update($id, $data)`

Update an existing record.

```php
$client->calls->update('Record ID', [
    'Field 1' => 'Value 1',
    'Field 2' => 'Value 2',
    ...
]);
```

#### `updateMany($data)`

Update multiple existing records.

```php
$records = [
    [
        'ID' => 'Record 1 ID',
        'Field 1' => 'Value 1',
        'Field 2' => 'Value 2',
        ...
    ], [
        'ID' => 'Record 2 ID',
        'Field 1' => 'Value 1',
        'Field 2' => 'Value 2',
        ...
    ],
    ...
];

$client->calls->updateMany($records);
```

#### `delete($id)`

Delete a record.

```php
$client->calls->delete('Record ID');
```

#### `deleteMany($ids)`

Delete multiple records.

```php
$client->calls->deleteMany(['Record 1 ID', 'Record 2 ID']);
```

#### `deleteAttachedFile($attachmentId)`

Delete a file attached to a record.

```php
$client->calls->deleteAttachedFile('Attachment ID');
```

### Fields meta-module

It is a meta-module that is attached to each records module to retrieve information about its fields.

You access it through the `fields()` method of a module: `$client->contacts->fields()`.

#### `sections(array $params = [])`

Query on method `getFields`.

```php
$client->contacts->fields()->sections($params);
// is equivalent to
$client->contacts->newQuery('getFields', $params);
```

In the raw API response, fields are grouped by sections (labeled groups of fields), that is why this helper method is named like this.

#### `getAll(array $params = [])`

Retrieve all fields of the module. Return a collection of `Zoho\Crm\Entities\Field` entities.

```php
$fields = $client->contacts->getAll();
```

#### `getNative()`

Retrieve the native fields of the module.

```php
$fields = $client->contacts->getNative();
```

#### `getCustom()`

Retrieve the custom fields of the module.

```php
$fields = $client->contacts->getCustom();
```

#### `getSummary()`

Retrieve the summary fields of the module. The summary is the section at the top of a Zoho record page.

```php
$fields = $client->contacts->getSummary();
```

#### `getMandatory()`

Retrieve the mandatory fields of the module.

```php
$fields = $client->contacts->getMandatory();
```

## Advanced topics

### Custom modules

If you have custom modules in your Zoho organization, you may want to request them through the API too.

To support your own custom module, you need to create a dedicated class which extends `Zoho\Crm\Api\Modules\AbstractRecordsModule`. In this class, you need to re-define 2 properties:
1. `$name`: the name of the module (not the "display name"!)
2. `$supportedMethods`: the list of API methods that you can use on the module

And an optional one: `$associatedEntity`, which is the class of the entity object.

Example:

```php
use Zoho\Crm\Api\Modules\AbstractRecordsModule;

class MyCustomModule extends AbstractRecordsModule
{
    protected static $name = 'CustomModule1';

    protected static $associatedEntity = MyCustomThing::class;

    protected static $supportedMethods = [
        'getFields',
        'getRecordById',
        'getRecords',
        'getMyRecords',
        'searchRecords',
        'insertRecords',
        'updateRecords',
        'deleteRecords',
        'getDeletedRecordIds',
        'getRelatedRecords',
        'getSearchRecordsByPDC',
    ];
}
```

Then you have to attach this custom module to the client:

```php
$client->attachModule(MyCustomModule::class);
```

After that you can use your custom module just like any other:

```php
$client->module('CustomModule1');
// or
$client->customModule1;
```

#### Define an alias for a custom module

Custom modules in Zoho are all named like that: "CustomModuleX" where 'X' is the number.

If you want to use a more eloquent name, you can define an alias for it while attaching it:

```php
$client->attachModule(MyCustomModule::class, 'MyCustomThings');
```

and then:

```php
$client->module('MyCustomThings');
// or
$client->myCustomThings;
```

### Use a different API endpoint

By default, the endpoint is: `https://crm.zoho.com/crm/private/`. You may want to use another one: https://www.zoho.com/crm/developer/docs/api/using-api-url.html

For that, you can pass a second argument to the `Client` constructor:

```php
$client = new Zoho\Crm\Client('MY_ZOHO_AUTH_TOKEN', 'https://crm.zoho.eu/crm/private/');
```

Or use the `setEndpoint()` method:

```php
$client->setEndpoint('https://crm.zoho.eu/crm/private/');
```

### Hide the auth token in exception messages

When something goes wrong with an HTTP request, an exception containing the requested URL can be raised. Because the API auth token is a URL parameter, it can then be seen in the exception message.

This exception message could end up in many "unsafe" places like server logs, error monitoring services, company internal communication etc. For example it could be displayed on Slack via a Rollbar integration. For this reason you could want to remove the auth token from exception messages.

Just set the `"exception_messages_obfuscation"` preference to `true`:

```php
$zoho->preferences()->enable('exception_messages_obfuscation');
```
