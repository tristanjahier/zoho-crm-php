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
