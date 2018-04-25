# Zoho CRM API wrapper library (PHP)

This is an API wrapper library for Zoho CRM, written in PHP.

It aims to cover the whole API (every module and method), while providing a great abstraction and very easy-to-use methods.

## Requirements

- PHP : `5.5+`
- PHP cURL extension enabled

## Get started

This package is currently at an early development stage. Full documentation will come when it is stable enough.

### A quick example

```php
// Create a Zoho client
$zoho = new Zoho\CRM\Client('MY_ZOHO_AUTH_TOKEN');

// Use its supported modules to make easy requests...
$one_lead = $zoho->leads->getById('1212717324723478324');
$many_leads = $zoho->leads->getByIds(['8734873457834574028', '3274736297894375750']);
$admins = $zoho->users->getAdmins();

// ...or build them manually
$response = $zoho->request('Module', 'method', ['a_parameter' => 'blablebloblu']);
```

## Summary

* [Generate Auth Token](#user-content-generate-auth-token)
* [Response Objects](#user-content-response-objects)
* [API Usage](#user-content-api-usage)
* [Unit Tests](#user-content-unit-tests)

## Generate Auth Token

[Source] https://www.zoho.com/crm/help/api/using-authentication-token.html#Generate_Auth_Token

* Connect to https://accounts.zoho.com
* Send request to 

```
https://accounts.zoho.com/apiauthtoken/nb/create?SCOPE=ZohoCRM/crmapi&EMAIL_ID=[Username/EmailID]&PASSWORD=[Password]&DISPLAY_NAME=[ApplicationName]
```

* Expected response

```
#
#Mon Apr 23 07:36:42 PDT 2018
AUTHTOKEN=8c40d6720636c6bb2eadace2d2243ed1
RESULT=TRUE
```

## Response Objects

### RequestPaginator

| Methods | Comments | Response |
|---|---|---|
| fetch  |  execute query for the next page available |  [Response](#user-content-response) Object  |
| fetchAll |  execute queries for all pages available |  Array of [Response](#user-content-response)  |
| getResponses | all responses fetched |  Array of [Response](#user-content-response)  |
| getNumberOfPagesFetched | amount of pages fetched |  Integer  |

### Response

| Methods | Comments | Response |
|---|---|---|
| getContent  |  get JSON parsed response |  Object  |
| getRawData  |  get raw response |  Object  |

## API Usage

### Available modules

By default, some modules are enabled in ```src/Client.php```

* Info
* Users
* Leads
* Potentials
* Calls
* Contacts
* Products

### Available methods

Every modules (except Users) have the following methods (```src/Api/Modules/AbstractRecordsModule.php```):

* getAll
* getById
* getMine
* search
* getBy
* getRelatedById
* exists
* insert
* insertMany
* update
* updateMany
* delete
* deleteMany
* getDeletedIds


### Get all records

* **Method:** ```getAll```
* **Data Params**
* **Code sample**

```php
require './vendor/autoload.php';

// Create a Zoho client
$zoho = new Zoho\CRM\Client('0c85ee5db4119df7ad21bb9581d08670');

/**
 * @var $many_leads \Zoho\CRM\Api\RequestPaginator
 */
$leads = $zoho->leads->getAll();

echo '<pre>';
print_r($leads->fetch()->getContent());
echo '</pre>';
```

* **Response**

[RequestPaginator](#user-content-requestpaginator) Object

### Get one specific record

* **Method:** ```getById```
* **Data Params**
Record ID
* **Code sample**

```php
require './vendor/autoload.php';

// Create a Zoho client
$zoho = new Zoho\CRM\Client('0c85ee5db4119df7ad21bb9581d08670');

$lead = $zoho->leads->getById('3211639000000152457');

echo '<pre>';
print_r($lead->getContent());
echo '</pre>';
```

* **Response**

[Response](#user-content-response) Object

### Insert one record

* **Method:** ```insert```
* **Data Params**
Array
* **Code sample**

```php
require './vendor/autoload.php';

// Create a Zoho client
$zoho = new Zoho\CRM\Client('0c85ee5db4119df7ad21bb9581d08670');

$zoho->leads->insert([
    'Company' => 'TEST',
    'Last Name' => 'TEST'
]);
```

* **Response**

[Response](#user-content-response) Object

### Delete one record

* **Method:** ```delete```
* **Data Params**
Record ID
* **Code sample**

```php
require './vendor/autoload.php';

// Create a Zoho client
$zoho = new Zoho\CRM\Client('0c85ee5db4119df7ad21bb9581d08670');

$zoho->leads->delete('3211639000000152457');
```

* **Response**

[Response](#user-content-response) Object

### Delete multiple records

* **Method:** ```deleteMany```
* **Data Params**
Array
* **Code sample**

```php
require './vendor/autoload.php';

// Create a Zoho client
$zoho = new Zoho\CRM\Client('0c85ee5db4119df7ad21bb9581d08670');

$leads = $zoho->leads->deleteMany(['3211639000000152553', '3211639000000152560']);

echo '<pre>';
print_r($leads);
echo '</pre>';
```

* **Response**

[Response](#user-content-response) Object

```json
{"result":{"code":"5000","message":"Record Id(s) : 3211639000000152553;3211639000000152560,Record(s) deleted successfully"}
```

### Search by Criteria

* **Method:** ```search```
* **Data Params**
String
* **Code sample**

```php
require './vendor/autoload.php';

// Create a Zoho client
$zoho = new Zoho\CRM\Client('0c85ee5db4119df7ad21bb9581d08670');

$leads = $zoho->leads->search('((Company:TEST)OR(Last Name:TEST))');

echo '<pre>';
print_r($leads->fetch()->getContent());
echo '</pre>';
```

* **Response**

[RequestPaginator](#user-content-requestpaginator) Object

### Search by one specific Criteria

* **Method:** ```getBy```
* **Data Params**
String, String
* **Code sample**

```php
require './vendor/autoload.php';

// Create a Zoho client
$zoho = new Zoho\CRM\Client('0c85ee5db4119df7ad21bb9581d08670');

$leads = $zoho->leads->getBy('Company', 'TEST');

echo '<pre>';
print_r($leads->fetch()->getContent());
echo '</pre>';
```

* **Response**

[RequestPaginator](#user-content-requestpaginator) Object

### Update one record

* **Method:** ```update```
* **Data Params**
ID, Data
* **Code sample**

```php
require './vendor/autoload.php';

// Create a Zoho client
$zoho = new Zoho\CRM\Client('0c85ee5db4119df7ad21bb9581d08670');

$lead = $zoho->leads->update('3211639000000155006', [
    'Company' => 'TEST99'
]);

echo '<pre>';
print_r($lead->getContent());
echo '</pre>';
```

* **Response**

[Response](#user-content-response) Object

### Update multiple records

* **Method:** ```updateMany```
* **Data Params**
Data
* **Code sample**

```php
require './vendor/autoload.php';

// Create a Zoho client
$zoho = new Zoho\CRM\Client('0c85ee5db4119df7ad21bb9581d08670');

$leads = $zoho->leads->updateMany([
    [
        'Id' => '3211639000000158001',
        'Company' => 'Company modified'
    ],
    [
        'Id' => '3211639000000155013',
        'Company' => 'Company modified 2'
    ]
]);

echo '<pre>';
print_r($leads->getRawData());
echo '</pre>';
```

* **Response**

[Response](#user-content-response) Object

## Unit Tests

* Set AUTH_TOKEN in ```phpunit.xml```
* Run by executing ```./vendor/bin/phpunit``` command