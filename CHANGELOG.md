# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

https://github.com/tristanjahier/zoho-crm-php/compare/0.4.0...master

### Added

- Support for version 2 of the API. Components specific to this version are under the `Zoho\Crm\V2` namespace. Notably, the client class is `Zoho\Crm\V2\Client`.
  - The API support is divided into "sub-APIs", which are helpers that regroup multiple related features of the API. They are attached to the client and you can access them as public properties (e.g.: `$client->theSubApi`). Currently available sub-APIs are `records` and `users`. See README.md for further documentation.
  - The client needs an "access token store" object to handle its API access token persistency. There are multiple basic implementations available in namespace `Zoho\Crm\V2\AccessTokenStores`. It must implement `StoreInterface`.
- Compatibility with Guzzle 7.
- Compatibility with PHP 8.
- Compatibility with Doctrine Inflector 2.
- `Zoho\Crm\Contracts\ErrorHandlerInterface`.
- `createFromString` static method to `Zoho\Crm\Support\UrlParameters`.
- `getRaw` method to all queries (v1 and v2), to get the raw contents of the API response.
- `Zoho\Crm\V2\Scopes` utility class.
- `Zoho\Crm\Utils\OAuthHelper` utility class.

### Changed

- Composer package has been renamed `tristanjahier/zoho-crm`.
- Everything specific to version 1 of the API has been moved under the dedicated `Zoho\Crm\V1` namespace. Notably, the client class is now `Zoho\Crm\V1\Client`.
- Dependency injection in `Zoho\Crm\QueryProcessor` is now mandatory. Arguments `$requestSender` and `$responseParser` are not optional anymore, and `$errorHandler` has been added.
- Denominations of "HTTP verb" have been changed for "HTTP method" everywhere in the code. Because of that, the `getHttpVerb` method of `Zoho\Crm\Contracts\RequestableInterface` has been renamed `getHttpMethod`, and the `Zoho\Crm\Support\HttpVerb` class has been renamed `Zoho\Crm\Support\HttpMethod`.
- Denominations of "URI" have been changed for "URL" everywhere in the code. More information below.
- In `RequestableInterface`, method `setUri` has been removed. Other methods have been renamed: `getUri => getUrl`, `setUriParameter => setUrlParameter`. New methods have been added: `getUrlParameters`, `getUrlParameter`, `hasUrlParameter`, `removeUrlParameter`. Query implementations have been changed accordingly. Notably:
  - Multiple methods of `Zoho\Crm\V1\Query` have been renamed: `setUriParameter => setUrlParameter`, `getParameters => getUrlParameters`, `getParameter => getUrlParameter`, `hasParameter => hasUrlParameter`. `resetParameters` has also been renamed to `resetUrlParameters` for consistency.
- In `PaginatedQueryInterface`, method `paginated` has been removed, method `isPaginated` has been renamed `mustBePaginatedAutomatically`, and 2 other methods were added: `mustBePaginatedConcurrently` and `getConcurrency`. Query implementations have been changed accordingly. Furthermore:
  - Method `paginated`, used to turn on and off the automatic pagination of queries, has been renamed `autoPaginated`.
  - Method `mustFetchPagesAsynchronously`, used to determine if pages must be fetched concurrently, has been renamed `mustBePaginatedConcurrently`.
- In `QueryInterface`, method `getClient` was added.
- `Zoho\Crm\Support\UrlParameters` now supports string-casting for all implementations of `DateTimeInterface`, instead of just instances of `DateTime`.
- `Zoho\Crm\Support\Collection` now implements `Zoho\Crm\Support\Arrayable`.

### Deprecated

- Installing from old Composer package name `tristanjahier/zoho-crm-php`.
