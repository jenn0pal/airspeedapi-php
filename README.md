# Unofficial Airspeed PHP Client

## Installation

To install, run command:
```
composer require jenn0pal/airspeedapi-php:~1.0
```

## Usage

Refer to AirspeedAPI documentation for parameters


```php
use jenn0pal\Api\AirspeedApi;

$api = new AirspeedApi([
    'url' => 'API URL',
    'username' => 'USERNAME',
    'password' => 'PASSWORD',
    'api_key' => 'APIKEY',
    'token' => 'TOKEN'
]);

 $quote_data = [
    "merchantID" => 2,
    "serviceType" => "Door to Door",
    ...
 ];

// Get Quotation
$response = $api->quote($quote_data);
print_r((string) $response->getBody());

// {"ResponseCode":200,"ResponseDetail":"Qoute Generated","deliveryCharge":100.0000,"remarks":"Calculation Success","returnNum":1}


 $pickup_data = [
    "merchantID" => 2,
    "serviceType" => "Door to Door",
    ...
 ];

// Pickup
$response = $api->pickup($pickup_data);
print_r((string) $response->getBody());

// {"ResponseCode":200,"ResponseDetail":"Waybill Updated","id":0,"trackingRefNo":"PXAIR00001" ,"remarks":"Transaction Success","returnNum":2}
```
