# UpBank PHP SDK (Unofficial)
UpBank unofficial PHP SDK for the [UpBank API](https://developer.up.com.au/)

<!-- BADGES_START -->
[![Latest Version][badge-release]][packagist]
[![PHP Version][badge-php]][php]
![tests](https://github.com/YahaayLabs/upbank-php-sdk/workflows/tests/badge.svg)
[![Total Downloads][badge-downloads]][downloads]

[badge-release]: https://img.shields.io/packagist/v/yahaay-labs/upbank-php-sdk.svg?style=flat-square&label=release
[badge-php]: https://img.shields.io/packagist/php-v/yahaay-labs/upbank-php-sdk.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/yahaay-labs/upbank-php-sdk.svg?style=flat-square&colorB=mediumvioletred

[packagist]: https://packagist.org/packages/yahaay-labs/upbank-php-sdk
[php]: https://php.net
[downloads]: https://packagist.org/packages/yahaay-labs/upbank-php-sdk
<!-- BADGES_END -->

### UpBank Developer Page
Visit https://developer.up.com.au/

### Getting your UpBank Personal Access Token
Visit https://api.up.com.au/getting_started

## Installation
```shell
composer require yahaay-labs/upbank-php-sdk
```

## Usage
```shell
<?php

require './vendor/autoload.php';

use YahaayLabs\UpBank\Client;

$access_token = "[UPBANK ACCESS TOKEN]";

$client = new Client($access_token);

//Get All Accounts in an array of objects
$accounts = $client->accounts->all()->data();

array_walk( $accounts, function($account) {
    echo "{$account->id} => {$account->attributes->displayName} <br/>";
});

```

### Getting Records
```shell
<?php

//Get All Accounts
$accounts = $client->accounts->all()->data();

//Get All Categories
$accounts = $client->categories->all()->data();

//Get All Tags
$accounts = $client->tags->all()->data();

//Get All Transactions
$accounts = $client->transactions->all()->data();

//Getting specific record
$transactionID = "[TRANSACTION ID]";
$transaction = $client->transactions->get($transactionID)->getRaw();
```


## Testing
Adding using test is still in the works

To run the test:
```bash
composer run test
```

## Credits

- [Gerald Villacarlos](https://github.com/eLBirador)
- [All Contributors](../../contributors)

## Contributing
Contributions are more than welcome! See [CONTRIBUTING.md](/CONTRIBUTING.md) for more info.

## LICENSE
MIT license. Please see [LICENSE](LICENSE) for more info.