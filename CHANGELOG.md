# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

https://github.com/tristanjahier/zoho-crm-php/compare/0.5.0...master

### Changed

- Dropped support for PHP 7.
- Dropped support for Guzzle 6.
- Dropped support for Doctrine Inflector 1.
- `Zoho\Crm\V2\ResponseParser` will now throw an `UnreadableResponseException` when the API response body cannot be parsed.

### Removed

- Support for version 1 of the API. The whole `Zoho\Crm\V1` namespace has been deleted.
- Unused classes `Zoho\Crm\ResponseFormat` and `Zoho\Crm\IdList`.
- Trait `Zoho\Crm\Support\ClassShortNameTrait`. Replaced by `Zoho\Crm\Support\Helper::getClassShortName`.

### Development

- Upgraded dependencies:
  - `symfony/var-dumper`: `5 -> 6`
- Required PsySH (`psy/psysh`) as a development dependency (instead of relying only on a global installation).


## [0.5.0] - 2023-09-03

### Added

- Support for version 2 of the API. Components specific to this version are under the `Zoho\Crm\V2` namespace. Notably, the client class is `Zoho\Crm\V2\Client`.
  - The API support is divided into "sub-APIs", which are helpers that regroup multiple related features of the API. They are attached to the client and you can access them as public properties (e.g.: `$client->theSubApi`). Currently available sub-APIs are `records` and `users`. See [README.md](/README.md) for further documentation.
  - The client needs an "access token store" object to handle its API access token persistency. There are multiple basic implementations available in namespace `Zoho\Crm\V2\AccessTokenStores`. It must implement `StoreInterface`.
- Compatibility with Guzzle 7.
- Compatibility with PHP 8.
- Compatibility with Doctrine Inflector 2.
- `Zoho\Crm\Contracts\ErrorHandlerInterface`.
- `createFromString` static method to `Zoho\Crm\Support\UrlParameters`.
- `getRaw` method to all queries (v1 and v2), to get the raw contents of the API response.
- `Zoho\Crm\V2\Scopes` utility class.
- `Zoho\Crm\Utils\OAuthHelper` utility class.
- `Zoho\Crm\PreferencesContainer` base class for client preferences. It adds a new `isSet` method to check if a preference value is not `null`.

### Changed

- Composer package has been renamed `tristanjahier/zoho-crm`.
- Everything specific to version 1 of the API has been moved under the dedicated `Zoho\Crm\V1` namespace. Notably, the client class is now `Zoho\Crm\V1\Client`.
- Dependency injection in `Zoho\Crm\QueryProcessor` is now mandatory. Arguments `$requestSender` and `$responseParser` are not optional anymore, and `$errorHandler` has been added.
- `Zoho\Crm\RequestSender` constructor does not accept a `$preferences` argument anymore.
- Denominations of "HTTP verb" have been changed for "HTTP method" everywhere in the code. Because of that, the `getHttpVerb` method of `Zoho\Crm\Contracts\RequestableInterface` has been renamed `getHttpMethod`, and the `Zoho\Crm\Support\HttpVerb` class has been renamed `Zoho\Crm\Support\HttpMethod`.
- Method `setHttpMethod` of queries now throws an exception (`Zoho\Crm\Exceptions\InvalidHttpMethodException`) when an invalid value is provided.
- Denominations of "URI" have been changed for "URL" everywhere in the code. More information below.
- In `RequestableInterface`, method `setUri` has been removed. Other methods have been renamed: `getUri => getUrl`, `setUriParameter => setUrlParameter`. New methods have been added: `getUrlParameters`, `getUrlParameter`, `hasUrlParameter`, `removeUrlParameter`. Query implementations have been changed accordingly. Notably:
  - Multiple methods of `Zoho\Crm\V1\Query` have been renamed: `setUriParameter => setUrlParameter`, `getParameters => getUrlParameters`, `getParameter => getUrlParameter`, `hasParameter => hasUrlParameter`. `resetParameters` has also been renamed to `resetUrlParameters` for consistency.
- In `PaginatedQueryInterface`, method `paginated` has been removed, method `isPaginated` has been renamed `mustBePaginatedAutomatically`, and 2 other methods were added: `mustBePaginatedConcurrently` and `getConcurrency`. Query implementations have been changed accordingly. Furthermore:
  - Method `paginated`, used to turn on and off the automatic pagination of queries, has been renamed `autoPaginated`.
  - Method `mustFetchPagesAsynchronously`, used to determine if pages must be fetched concurrently, has been renamed `mustBePaginatedConcurrently`.
- In `QueryInterface`, method `getClient` was added.
- `Zoho\Crm\Support\UrlParameters` now supports string-casting for all implementations of `DateTimeInterface`, instead of just instances of `DateTime`.
- `Zoho\Crm\Exceptions\InvalidQueryException` now shows the HTTP method in its message.
- `Zoho\Crm\Support\Collection` now implements `Zoho\Crm\Support\Arrayable`.

### Fixed

- Client ID, client secret and refresh token are now sent to the authorization endpoint in the request body instead of the URL query string. This is considered a fix because it improves security by reducing the risk of exposing these secrets in clear in error messages.
- Deprecation warnings for return types related to `ArrayAccess`, `Countable` and `IteratorAggregate`.
- Deprecation warnings related to function `http_build_query`.

### Deprecated

- Installing from old Composer package name `tristanjahier/zoho-crm-php`.

### Development

- Upgraded dependencies:
  - `phpunit/phpunit`: `5 -> 9`
  - `symfony/var-dumper`: `4 -> 5`


## [0.4.0] - 2020-09-26

### Added

- Documentation comments everywhere in the code (classes, properties, methods...).
- Documentation/developer guide in README.md to teach all the basics to use this library. The guide is not an exhaustive technical documentation but should cover a vast majority of the use cases.
- Support for 12 new modules: Accounts, Campaigns, Cases, ContactRoles, Deals, Invoices, PriceBooks, PurchaseOrders, Quotes, SalesOrders, Solutions, Vendors.
- `triggerWorkflowRules`, `unselect`, `selectAll`, `selectTimestamps`, `selectDefaultColumns` methods to `Zoho\Crm\Api\Query`.
- `apiModules()` method to `Info` module class.
- New methods to `Zoho\Crm\Support\Collection`: `sum`, `collapse`, `flatMap`, `except`, `intersect`, `random`.
- New methods to `Zoho\Crm\Entities\Collection`: `uniqueById`.
- `Zoho\Crm\Entities\Records\Record` as a base class for all record entities (e.g. `Call`, `Contact`, `Lead`, `Product` etc.).
- `Zoho\Crm\Entities\ImmutableEntity`.
- `relationsOf()` method to all module classes.
- `stageHistoryOf()` and `contactRolesOf()` to `Deals` module class.
- "Before query execution" and "after query execution" hooks.
- Support for concurrent queries.
- Client preferences:
  - `concurrent_pagination_by_default` (default `false`) to enable concurrency by default for all paginated queries.
  - `default_concurrency` (default `5`) the number of concurrent API requests by default.
- Interfaces (also referred to as "contracts") in `Zoho\Crm\Contracts` namespace: `ClientInterface`, `QueryInterface`, `PaginatedQueryInterface`, `ResponseInterface`, `MiddlewareInterface`, `RequestableInterface`, `QueryPaginatorInterface`, `ResponsePageMergerInterface`, `ResponseTransformerInterface`, `RequestSenderInterface`, `ResponseParserInterface`. These interfaces define the most important interconnected components of the API client, and are part of the foundations for more flexibility, customization and testability of the library.
- Query middleware. Registered via the client, applied to all executing queries.
- `Zoho\Crm\Api\UrlParameters::createFromUrl()`.
- `getUrlPathSegments()` and `getUrlPathSegmentByIndex()` methods to `Zoho\Crm\Support\Helper`.
- `Zoho\Crm\Api\RawQuery` query class, allowing developers to define any API request manually.
- `Zoho\Crm\Client::newRawQuery()`.

### Changed

- Dropped support for PHP < 7.3.
- Renamed `Client::DEFAULT_FORMAT` into `Client::DEFAULT_RESPONSE_FORMAT`.
- Changed entities string-cast result. It now returns a prettified JSON with the entity type and its properties.
- `'getDeletedRecordIds'` API method returns an empty array when the response is empty, instead of `null`.
- Letter case of all variables and properties has been changed from _snake_case_ to _camelCase_.
- Renamed entity "properties" into "attributes". It does not affect the use of entity objects, except for casting to string (`__toString()`) and serialization (`__sleep()`).
- The primary key is not defined in the modules anymore, it is defined at the entity level. For this purpose:
  - the `$primaryKey` static property and `primaryKey()` static method were removed from modules,
  - an `$idName` static property and an `idName()` static method have been added to entities,
  - entities' method `key()` was replaced with `getId()`,
  - entity collections' method `entityKeys()` with `entityIds()`.
- Renamed `PotStageHistory` entity into `PotentialStageHistoryEntry`.
- Moved the "query processing" code from the `Zoho\Crm\Client` class to a dedicated `Zoho\Crm\QueryProcessor` class which acts as an engine. This engine has 2 components to handle the different steps of query execution:
  - `Zoho\Crm\RequestSender` sends the HTTP requests and returns the raw response of the API.
  - `Zoho\Crm\ResponseParser` parses the raw API response then transforms it to make it easier to read and use (replaces static helper `Zoho\Crm\Api\ResponseParser`).
  - Both of these components can be manually injected in the query processor via its constructor, as long as they implement their respective interfaces, `RequestSenderInterface` and `ResponseParserInterface`.
- Made API method handlers used as instances attached to the `Client` instead of static helpers.
  - Interface `Zoho\Crm\Api\Methods\MethodInterface` received a lot of changes:
    - methods `responseContainsData()` and `expectsMultipleRecords()` were removed,
    - method `tidyResponse()` was renamed into `cleanResponse()`,
    - methods `getHttpVerb()`, `isResponseEmpty()`, `getEmptyResponse()` and `convertResponse()` were added,
    - all methods are now instance methods (not static anymore).
  - Base method handler `Zoho\Crm\Api\Methods\AbstractMethod`:
    - method `getResponseDataType()` was removed.
  - `Client::registerMethodHandler()` now requires any implementation of `MethodInterface` (instead of a class extending `AbstractMethod` before).
- The response-parsing logic was completely revamped. The content of the API response is now parsed and transformed inside `Zoho\Crm\ResponseParser` and the resulting `Response` object will not be modified afterwards. To achieve that, a lot of changes were made:
    - all content post-processing (e.g. conversion into entity objects or collections) was moved to API method handlers. This was formerly done by the method `getQueryResults()` of `Client`, which was removed.
    - paginated responses merger is now handled by the query processor instead of the query paginator. The merger logic is defined in the API method handlers thanks to a new interface `Zoho\Crm\Api\Methods\MethodWithPaginationInterface`.
    - the `Zoho\Crm\Api\Response` class was pruned of many methods (`getType()`, `containsRecords()`, `hasSingleRecord()`, `hasMultipleRecords()`, `isConvertibleToEntity()`, `toEntity()`, `toEntityCollection()`) and properties (`$type`, `$hasMultipleRecords`).
- Collections use `array_key_first()` and `array_key_last()` to select their ends (instead of `reset()` and `end()`).
- Replaced `AbstractEntity` with `Entity`, which is a default minimal implementation instead of an abstract.
- Responses to the `getFields` API method will now be encapsulated in `FieldSection` and `Field` entities. `Zoho\Crm\Api\Modules\ModuleFields::getAll()` returns an entity collection of `Zoho\Crm\Entities\Field`.
- Responses to the `getModules` API method will now be encapsulated in collections of `Module` entities.
- `insert()` and `update()` methods of `Zoho\Crm\Api\Modules\AbstractRecordsModule` now return single values instead of arrays.
- Methods `format`, `module`, `method` and `limit` of `Zoho\Crm\Api\Query` accept `null` to unset the related value.
- `Zoho\Crm\Api\Query::select()` is now cumulative. It does not overwrite the previous selection, it adds up to it.
- `Zoho\Crm\Api\UrlParameters::__toString()` was improved.
- Removed `Zoho\Crm\Api` namespace. All contents have been moved to the parent namespace `Zoho\Crm`. In this release change log there might still be references to this namespace.
- Moved `Zoho\Crm\HttpVerb` to `Zoho\Crm\Support\HttpVerb`, add missing verbs (`HEAD`, `PUT`, `DELETE`, `PATCH`, `OPTIONS`, `TRACE`, `CONNECT`), and add 2 helper methods: `getAll` and `isValid`.

### Fixed

- `Zoho\Crm\Support\Collection::binarySearch()`.
- `Zoho\Crm\Support\Collection::getItemPropertyValue()` for undefined array indexes.
- XML data will now correctly be sent in the request body instead of the URL query string.
- `Zoho\Crm\Api\QueryPaginator::applyQueryConstraints()`: limit of fetched records and maximum modification date.
- `Zoho\Crm\Api\Query::getSelectedColumns()`.
- `Zoho\Crm\Api\QueryPaginator::handlePageResponse()`: partially filled response pages will correctly stop the pagination.
- `Zoho\Crm\Client::newQuery()` without arguments.

### Removed

- The property aliases feature of entities.
- Class `Zoho\Crm\Api\ResponseParser`.
- Class `Zoho\Crm\Api\ResponseDataType`.
- Methods `getMethodClass()` and `getEntityClass()` and `BASE_NAMESPACE` constant from `Zoho\Crm\Support\Helper`.
- `Zoho\Crm\Api\Query::paginate()`.

### Development

- Renamed `dev_material/` directory into `dev/`. So the autoloaded development sources directory is now `dev/src/`.


## [0.3.0] - 2019-04-19

### Added

- Installation instructions in README.md.
- A generic collection class: `Zoho\Crm\Support\Collection`. It supports a lot of common array-like operations and has a powerful `where()` method to filter its content.
- An `Arrayable` interface to ensure the presence of `toArray()` method.
- Methods to ease handling boolean-type preferences: `enable()`, `disable()` and `isEnabled()`.
- "exception_messages_obfuscation" preference to hide the API auth token in request exception messages.
- `newEntity()` method to modules.
- A reference to the client and module in entities.
- `key()` method to entities and `entityKeys()` to entity collections.
- Ability to use a custom API endpoint.
- Ability to define module aliases. Useful for custom modules whose names are awful (CustomModule1...)
- Support for API error 4421 through `RequestLimitExceededException`.
- `primaryKey()` method to `Users` module.
- Entities serialization.

### Changed

- Dropped support for PHP 5.5. Minimum requirement is now PHP 7.1.
- Renamed namespace `Zoho\CRM` into `Zoho\Crm`.
- Renamed `Exception` namespaces into `Exceptions`.
- Replaced `Request` class with `Query` class, which is more flexible and provides method-chaining possibilities. Also replaced `RequestPaginator` with `QueryPaginator`.
- More generally, completely redesigned the request pipeline.
- Removed class `RequestLauncher`, moved the request counter to `Client`.
- Removed `ModuleFields` instance (e.g. `$zoho->contacts->fields`) from modules that do not support the `getFields` API method.
- Removed `functions.php` and replaced them with a static class helper: `Zoho\Crm\Support\Helper`.
- Made entities `Collection`, `IdList`, `UrlParameters` and `Preferences` extend the new generic collection `Zoho\Crm\Support\Collection`.
- Removed all formerly existing client preferences. All queries are now strictly validated, paginated queries are now automatically fetched when needed, and the records are now always returned as entity objects when possible.
- Most of the modules methods now return a `Query` object (instead of executing a request), so that the developer can chain his own constraints before actually making a call to the API.
- Most of the modules methods have been renamed.
- Allowed to create a query by calling the name of an API method directly on a module instance. e.g. `$zoho->potentials->getRecords()`.
- Entities `toArray()` method now return the real Zoho property names instead of aliases. Use `toAliasArray()` to get the former result.
- Improved how modules are attached to the client and handled internally.

### Fixed

- A bug when trying to determine if we reached the maximum modification date while fetching pages in `QueryPaginator` (formerly `RequestPaginator`).
- URL parameters are now properly encoded.
- Parsing for a rare (but odd!) API response for module Events when there is no data.

### Development

- Autoload `dev_material/src` directory in development environment.
- Added custom casters for symfony/var-dumper and PsySH local config file (`.psysh.php`) to load them automatically.


## [0.2.0] - 2018-07-30

### Added

- Support for these modules: Events, Tasks, Notes and Attachments.
- Support for these API methods: `getSearchRecordsByPDC` and `deleteFile`.
- Support for 3 new comparison operators to entity collections: `'in'`, `'=~'` (regex) and `'like'`.
- `fetchLimit()` to `RequestPaginator`, to fetch a given number of pages.
- "validate_requests" preference.
- A request counter to `RequestLauncher`.

### Fixed

- A bug with `getRecords` response parsing.


## [0.1.0] - 2018-05-16

Initial development release.
TODO: describe briefly what this first version provided.
