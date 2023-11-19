# Zoho CRM API client (PHP)

This is an API client library for Zoho CRM, written in PHP.

It aims to cover the whole API (every module and method), while providing a great abstraction and very easy-to-use methods.

## Requirements

- PHP : `8.0+`
- PHP cURL extension enabled

## Installation

The recommended way to install this package is through [Composer](https://getcomposer.org).

Edit your `composer.json` file:

```json
"require": {
    "tristanjahier/zoho-crm": "^0.5"
}
```

or simply run this command:

```
composer require tristanjahier/zoho-crm
```

## Getting started

### TL;DR - A quick example

Here are just a few examples of what is possible to do with this library:

```php
// Create an API client
$client = new Zoho\Crm\V2\Client(
    new Zoho\Crm\V2\AccessTokenBroker('MY_API_CLIENT_ID', 'MY_API_CLIENT_SECRET', 'MY_API_REFRESH_TOKEN')
);

// Create a request and execute it
$response = $client->newRawRequest('Calls')->param('page', 2)->execute();

// Retrieve all deals modified for the last time after April 1st, 2019
$deals = $client->records->deals->all()->modifiedAfter('2019-04-01')->get();

// Retrieve records by ID
$myLead = $client->records->leads->find('1212717324723478324');
$myProduct = $client->records->products->find('8734873457834574028');

// Create a new contact
$result = $client->records->contacts->insert([
    'First_Name' => 'Jean',
    'Last_Name' => 'Dupont',
    'Email' => 'jacques@dupont.fr'
]);

$contactId = $result['details']['id'];

// Update the name of the contact
$client->records->contacts->update($contactId, ['First_Name' => 'Jacques']);

// Delete this contact
$client->records->contacts->delete($contactId);
```

### The basics

The main component of this library is the `Client` class. This is the **starting point** for each API request.

To create a client object you need an "access token broker" first. It is an object which sole purpose is to provide your client with fresh API access tokens. It MUST implement `Zoho\Crm\Contracts\AccessTokenBrokerInterface`.

`Zoho\Crm\V2\AccessTokenBroker` is the default implementation and it should suit most use cases. To create an instance you need to provide the credentials of your registered API client. In that order, the client ID, the client secret, and the refresh token:
```php
$tokenBroker = new Zoho\Crm\V2\AccessTokenBroker('MY_API_CLIENT_ID', 'MY_API_CLIENT_SECRET', 'MY_API_REFRESH_TOKEN');
```

Then you can create your client using that token broker:
```php
$client = new Zoho\Crm\V2\Client($tokenBroker);
```

It is sufficient to start making requests to the API of Zoho CRM. However, in this configuration, the API access token (that is used to authenticate requests) will only live as long as the `$client` instance. It means that as soon as your client is garbage-collected or your PHP script stops executing, you will lose your access token, even though it was probably still valid for many minutes!

To prevent wasting fresh access tokens, **it is strongly recommended to use an "access token store" to enable persistency** across multiple PHP lifecycles:
1. A token store is an object solely meant to handle the storage of the access token used by the client.
2. It MUST implement `Zoho\Crm\Contracts\AccessTokenStoreInterface`.
3. It shall be passed as the 2nd argument of the client constructor.

This library provides a few implementations in `Zoho\Crm\AccessTokenStorage`. To quickly get started you may use the `FileStore`, which, as its name suggests, simply stores the access token in the local file system. Example:
```php
$tokenStore = new Zoho\Crm\AccessTokenStorage\FileStore('dev/.token.json');
$client = new Zoho\Crm\V2\Client($tokenBroker, $tokenStore);
```

Finally, you need to make sure that your client has a valid access token (not expired):
```php
if (! $client->accessTokenIsValid()) {
    $client->refreshAccessToken();
}
```

You are now ready to make API requests! One possibility is to follow the HTTP specifications of the API and manually craft any request you want using "raw requests":
```php
// Retrieve the second page of records from the Contacts module, modified after April 1st, 2019:
$request = $client->newRawRequest()
    ->setHttpMethod('GET')
    ->setUrl('Contacts')
    ->setHeader('If-Modified-Since', '2019-04-01')
    ->setUrlParameter('page', 2);

// Retrieve a Deals record whose ID is 9032776450912388478:
$request = $client->newRawRequest('Deals/9032776450912388478');
```

Creating a request object does not make any HTTP request to the API, you need to execute it:
```php
$response = $request->execute();
```

If the request is successful, it returns a `Response` instance. The API response is parsed and cleaned up for you, you simply have to use `getContent()` to get your data:
```php
$data = $response->getContent();
```

All summarized:
```php
$response = $client->newRawRequest()
    ->setHttpMethod('GET')
    ->setUrl('Contacts')
    ->setHeader('If-Modified-Since', '2019-04-01')
    ->setUrlParameter('page', 2)
    ->execute();

$records = $response->getContent();
```

If you do not want to bother with the formal `Response` object, you can call the `get()` method on any request. It will execute the request and return its response content:
```php
$data = $request->get();
// is strictly equivalent to:
$data = $request->execute()->getContent();
```

*But... that's still a bit verbose, isn't it?* Yes. This is just the most basic way to make an API request. Read the next sections to learn how to make better use of the library.

### The client sub-APIs

The API support is divided into "sub-APIs", which are helpers that regroup multiple related features of the API. They are attached to the client and you can access them as public properties (e.g.: `$client->theSubApi`). The currently available sub-APIs are:

| Client accessor | Class |
| --------------- | ----- |
| `records` | `Zoho\Crm\V2\Records\SubApi` |
| `users` | `Zoho\Crm\V2\Users\SubApi` |

**The purpose of a sub-API is to provide to the developer a fluent, eloquent and concise interface to manipulate one or multiple related aspects of the API.**

For example, let's consider this request from a previous example, to retrieve the second page of records from the Contacts module that were modified after April 1st, 2019:
```php
$records = $client->newRawRequest()
    ->setHttpMethod('GET')
    ->setUrl('Contacts')
    ->setHeader('If-Modified-Since', '2019-04-01')
    ->setUrlParameter('page', 2)
    ->get();
```

Using the Records sub-API, it can be rewritten like so:
```php
$records = $client->records->contacts->all()->modifiedAfter('2019-04-01')->page(2)->get();
```

Creating a new contact is very straightforward:
```php
$result = $client->records->contacts->insert([
    'First_Name' => 'Jean',
    'Last_Name' => 'Dupont',
    'Email' => 'jean.dupont@exemple.fr'
]);
```

Retrieving all users from your Zoho CRM organization is as simple as:
```php
$users = $client->users->all()->get();
```

These are just a couple of examples. Sub-APIs bring many more features. Look at the dedicated documentation and explore the code to find out.

### Request pagination

When requesting records from Zoho, you will get a maximum of 200 records per response. Thus, if you want to get more than 200 records, you need to make multiple requests. This is done with the "page" URL parameter. Iterating on this parameter is called **pagination**.

In this library, pagination is made simple thanks to a request method called `autoPaginated()`. All you have to do is to call this method on a compatible request object (implementing `Zoho\Crm\Contracts\PaginatedRequestInterface`) and the library will fetch every page of records until there is no more data (or before if you set a limit). Example:
```php
$client->records->contacts->newListRequest()->autoPaginated()->get();
```

> [!NOTE]
> The request objects returned by the `all()` methods of Records and Users sub-APIs have auto-pagination already enabled.

By default, request pagination is synchronous. It simply means that every new page is only fetched once the previous one has been executed and returned a response. **This library also supports asynchronous request execution, and it usually makes pagination faster.** Once again, this is really simple to use. All you have to do is to call the `concurrency()` method on the request:
```php
$client->records->calls->newListRequest()->autoPaginated()->concurrency(5)->get();
// or
$client->records->calls->all()->concurrency(5)->get();
```

This method takes a single argument: a positive non-zero integer (> 0). It is the number of concurrent API requests. If you pass `1`, pagination will be synchronous. You can also pass `null` to disable asynchronous pagination.

Asynchronous pagination can speed up your paginated requests a lot, depending on the concurrency setting. If you need to retrieve thousands of records, it will save you a lot of execution time.

> [!WARNING]
> With X concurrent requests, you can waste up to X-1 API requests. Use it wisely.

### Response types

The type of a response content depends on the sub-API method you use. It can be a scalar like a string, an array, a boolean or null. But in most cases, you will get either an entity or a collection of entities.

Entities are objects containing a set of coherent data. For example, a Zoho record (contact, call, lead etc.) is an entity.

When the response contains (or should contain) multiple entities, you get an *entity collection*.

```php
// Returns a single entity of type Zoho\Crm\V2\Records\Record:
$client->records->calls->find('<record ID>');

// Returns a collection of entities (Zoho\Crm\V2\Users\User):
$client->users->all()->get();
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
$id = $contact->id;
$familyName = $contact->Last_Name;
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
$records[] = new Zoho\Crm\V2\Records\Record(['Phone' => '+1234567890']);

// And you can loop through it:
foreach ($records as $record) {
    ...
}
```

## Sub-APIs reference

### Records

The Records sub-API provides a single method, `module()`, used to create an instance of `Zoho\Crm\V2\Records\ModuleHelper`, which in turn provides a variety of features related to records.

```php
$client->records->module('Contacts');
$client->records->module('Calls');
$client->records->module('Deals');
$client->records->module('My_Custom_Module');
```

It also implements the magic method `__get()`, so that you can get a module helper using the module name in camel case as a public property:
```php
$client->records->contacts;
$client->records->calls;
$client->records->deals;
$client->records->priceBooks;
```

The rest of this section details the methods available on the module helper.

#### `all()`

Instance of `Zoho\Crm\V2\Records\ListRequest` with auto-pagination enabled.

```php
$client->records->deals->all();
```

#### `deleted()`

Instance of `Zoho\Crm\V2\Records\ListDeletedRequest` with auto-pagination enabled.

```php
$client->records->deals->deleted();
```

#### `search(string $criteria)`

Instance of `Zoho\Crm\V2\Records\SearchRequest` with auto-pagination enabled.

```php
$client->records->deals->search('<Search criteria>');
```

#### `searchBy(string $field, string $value)`

Instance of `Zoho\Crm\V2\Records\SearchRequest` with auto-pagination enabled.

```php
$client->records->deals->searchBy('Field', 'value');
// is shorthand for:
$client->records->deals->search('(Field:equals:value>)');
```

#### `relationsOf(string $recordId, string $relatedModule)`

List the records from another module related to a given record.
Instance of `Zoho\Crm\V2\Records\ListRelatedRequest` with auto-pagination enabled.

```php
$client->records->deals->relationsOf('<Deal ID>', 'Contacts');
```

#### `relatedTo(string $relatedModule, string $recordId)`

List the records related to a given record from another module. Inverse of `relationsOf()`.
Instance of `Zoho\Crm\V2\Records\ListRelatedRequest` with auto-pagination enabled.

```php
$client->records->deals->relatedTo('Contacts', '<Contact ID>');
```

#### `find(string $id)`

Retrieve a record by its ID.

```php
$record = $client->records->calls->find('Record ID');
```

Returns an instance of `Zoho\Crm\V2\Records\Record`, or `null` if not found.

#### `insert($record, array $triggers = null)`

Insert a new record.

Accepts a `Record` instance, or an array of attributes.

```php
$client->records->calls->insert([
    'Field_1' => 'Value 1',
    'Field_2' => 'Value 2',
    ...
]);
```

Returns an array containing information about the result of the operation.

#### `insertMany($records, array $triggers = null)`

Insert multiple new records at the same time.

```php
$records = [
    [
        'Field_1' => 'Value 1',
        'Field_2' => 'Value 2',
        ...
    ], [
        'Field_1' => 'Value 1',
        'Field_2' => 'Value 2',
        ...
    ],
    ...
];

$client->records->calls->insertMany($records);
```

Returns an array of arrays containing information about the results of the operation.

#### `update(string $id, $data, array $triggers = null)`

Update an existing record.

```php
$client->records->calls->update('Record ID', [
    'Field_1' => 'Value 1',
    'Field_2' => 'Value 2',
    ...
]);
```

Returns an array containing information about the result of the operation.

#### `updateMany($records, array $triggers = null)`

Update multiple existing records at the same time.

```php
$records = [
    [
        'id' => 'Record 1 ID',
        'Field_1' => 'Value 1',
        'Field_2' => 'Value 2',
        ...
    ], [
        'id' => 'Record 2 ID',
        'Field_1' => 'Value 1',
        'Field_2' => 'Value 2',
        ...
    ],
    ...
];

$client->records->calls->updateMany($records);
```

Returns an array of arrays containing information about the results of the operation.

#### `upsert($record, array $duplicateCheckFields = null, array $triggers = null)`

Upsert (update or insert) a record.

```php
$client->records->calls->upsert([
    'Field_1' => 'Value 1',
    'Field_2' => 'Value 2',
    ...
], ['Field_1']);
```

Returns an array containing information about the result of the operation.

#### `upsertMany($records, array $duplicateCheckFields = null, array $triggers = null)`

Upsert (update or insert) multiple records at the same time.

```php
$records = [
    [
        'Field_1' => 'Value 1',
        'Field_2' => 'Value 2',
        ...
    ], [
        'Field_1' => 'Value 1',
        'Field_2' => 'Value 2',
        ...
    ],
    ...
];

$client->records->calls->upsertMany($records, ['Field_1']);
```

Returns an array of arrays containing information about the results of the operation.

#### `delete(string $id)`

Delete a record.

```php
$client->records->calls->delete('Record ID');
```

Returns an array containing information about the result of the operation.

#### `deleteMany(array $ids)`

Delete multiple records at the same time.

```php
$client->records->calls->deleteMany(['Record 1 ID', 'Record 2 ID']);
```

Returns an array of arrays containing information about the results of the operation.

### Users

#### `all()`

Instance of `Zoho\Crm\V2\Users\ListRequest` with auto-pagination enabled.

```php
$client->users->all();
```

## Advanced topics

### Use a different API endpoint

By default the endpoint is `https://www.zohoapis.com/crm/v2/`. You may want to use another one: https://www.zoho.com/crm/developer/docs/api/v2/multi-dc.html.

For that, you can use the `setEndpoint()` method:
```php
$client->setEndpoint('https://www.zohoapis.eu/crm/v2/');
```

Similarly, **if you are using the default access token broker**, you can change the authorization endpoint:
```php
$client->getAccessTokenBroker()->setAuthorizationEndpoint('https://accounts.zoho.eu/oauth/v2/');
```

### Refresh the access token automatically

Out of the box, you need to deal with the access token validity by yourself. Meaning that you need to check on the expiry date regularly, and make a request to ask for a new token when it has expired.

The client has an option to automatically refresh its access token when needed. You simply have to set the `'access_token_auto_refresh_limit'` preference to the number of seconds of remaining validity below which it should be refreshed as soon as possible:
```php
$client->preferences()->set('access_token_auto_refresh_limit', 60);
```

In the above example, the client will request a fresh access token when it needs to make a request to the API and its current access token will expire in less than a minute.

### Before and after request execution hooks

If you need to, you can register a closure that will be executed **before** or **after** each request.

In both cases, the closure is an anonymous function which takes 2 arguments:
1. a copy of the request object ;
2. a unique ID of the execution (random 16 chars string), in case you need to match the "before" and "after" hooks.

Use the `beforeRequestExecution()` method to register a closure that will be invoked just before each request is executed, *but only after a successful request validation*.

Use the `afterRequestExecution()` method to register a closure that will be invoked just after each request is executed and the API has returned a response. *If an error or an exception is thrown from the HTTP request layer, the closure will not be invoked.*

Example:
```php
use Zoho\Crm\Contracts\RequestInterface;

$client->beforeRequestExecution(function (RequestInterface $request, string $execId) {
    // do something...
});

$client->afterRequestExecution(function (RequestInterface $request, string $execId) {
    // do something...
});
```

> [!NOTE]
> Paginated requests will not trigger these hooks directly, but their subsequent requests (per page) will.
> In other words, only the requests that directly lead to an API HTTP request will trigger the hooks.

### Request middleware

If you need to, you can register custom middleware that will be applied to each request before it is converted into an HTTP request. Unlike execution hooks, middleware can modify the request object. Actually, this is exactly the point of middleware.

Use the `registerMiddleware()` method, which only takes a `callable`. So, you can pass a closure or an object implementing `Zoho\Crm\Contracts\MiddlewareInterface`.

Example:
```php
use Zoho\Crm\Contracts\RequestInterface;

$client->registerMiddleware(function (RequestInterface $request) {
    $request->setUrlParameter('toto', 'tutu');
});
```

Notice that you don't need to return the request object. In fact, the return value will simply be ignored.

> [!NOTE]
> As with execution hooks, paginated requests will not pass through the middleware directly, but their subsequent requests (per page) will.
