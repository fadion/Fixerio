# Thin wrapper for Fixer.io

A thin wrapper for [Fixer.io](http://www.fixer.io), a service for foreign exchange rates and currency conversion. It provides a few methods to easily construct the url, makes the api call and gives back the response.

## Installation

- Add the package to your composer.json file and run `composer update`:
```json
{
    "require": {
        "fadion/fixerio": "~1.0"
    }
}
```

Laravel users can use the Facade for even easier access.

- Add `Fadion\Fixerio\ExchangeServiceProvider::class` to your `config/app.php` file, inside the `providers` array.
- Add a new alias: `'Exchange' => Fadion\Fixerio\Facades\Exchange::class` to your `config/app.php` file, inside the `aliases` array.

## Usage

Let's get the rates of EUR and GBP with USD as the base currency:

```php
use Fadion\Fixerio\Exchange;
use Fadion\Fixerio\Currency;

$exchange = new Exchange();
$exchange->key("YOUR_ACCESS_KEY");
$exchange->base(Currency::USD);
$exchange->symbols(Currency::EUR, Currency::GBP);

$rates = $exchange->get();
```

By default, the base currency is `EUR`, so if that's your base, there's no need to set it. The symbols can be omitted too, as Fixer will return all the supported currencies.

A simplified example without the base and currency:

```php
$rates = (new Exchange())->key("YOUR_ACCESS_KEY")->get();
```

The `historical` option will return currency rates for every day since the date you've specified. The base currency and symbols can be omitted here to, but let's see a full example:

```php
$exchange = new Exchange();
$exchange->key("YOUR_ACCESS_KEY");
$exchange->historical('2012-12-12');
$exchange->base(Currency::AUD);
$exchange->symbols(Currency::USD, Currency::EUR, Currency::GBP);

$rates = $exchange->get();
```

Finally, you may have noticed the use of the `Currency` class with currencies as constants. It's just a convenience to prevent errors from typos, but they're completely optional.

This:

```php
$exchange->base(Currency::AUD);
$exchange->symbols(Currency::USD, Currency::EUR, Currency::GBP);
```

is equivalent to:

```php
$exchange->base('AUD');
$exchange->symbols('USD', 'EUR', 'GBP');
```

Use whatever method fills your needs.

## Response

The response is a simple array with currencies as keys and ratios as values. For a request like the following:

```php
$rates = (new Exchange())->key("YOUR_ACCESS_KEY")->symbols(Currency::USD, Currency::GBP)->get();
```

the response will be an array:

```php
array('GBP' => 0.7009, 'USD' => 1.0666)
```

which you can access with the keys as strings or using the currency constants:

```php
print $rates['EUR'];
print $rates[Currency::GBP];
```

There is an option to handle the response as an object:

```php
$rates = (new Exchange())->key("YOUR_ACCESS_KEY")->symbols(Currency::USD, Currency::GBP)->getAsObject();

print $rates->USD;
print $rates->GBP;
```

The last option is to return the response as a `Result` class. This allows access to the full set of properties returned from the feed. 

```php
$result = (new Exchange())->key("YOUR_ACCESS_KEY")->symbols(Currency::USD, Currency::GBP)->getResult();

$date = $result->getDate(); // The date the data is from
$rates = $result->getRates(); // Array of rates as above
$usd = $result->getRate(Currency::USD); // Will return null if there was no value
```

## Error Handling

To handle errors, the package provides 2 exceptions. `ConnectionException` when http requests go wrong and `ResponseException` when the returned response from the api is not as expected. An example with exception handling:

```php
use Fadion\Fixerio\Exchange;
use Fadion\Fixerio\Exceptions\ConnectionException;
use Fadion\Fixerio\Exceptions\ResponseException;

try {
    $exchange = new Exchange();
    $exchange->key("YOUR_ACCESS_KEY");
    $rates = $exchange->get();
}
catch (ConnectionException $e) {
    // handle
}
catch (ResponseException $e) {
    // handle
}
```

## Laravel Usage

Nothing changes for Laravel apart from the Facade. It's just a convenience for a tad shorter way of using the package:

```php
use Exchange;
use Fadion\Fixerio\Currency;

$rates = Exchange::base(Currency::USD)->get();
```

To use this Facade, you should set your access key in your `config/services.php` file:

```php
'fixer'=>[
    'key'=>env("FIXER_ACCESS_KEY"),
]
```