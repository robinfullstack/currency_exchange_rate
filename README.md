# Currency Exchange Rate API

This project is a currency exchange rate API that fetches currency rates from the CurrencyFreaks API, saves them in a MySQL database with EUR as the base currency, and utilizes Redis for caching.

Symfony version: 5.4

## Dependencies

- Doctrine (with migrations)
- Redis
- GuzzleHTTP (for making HTTP requests)

## Installation

1. Clone the repository.
2. Run `composer install` to install the dependencies.
3. Create the database with the following command:
```shell
php bin/console doctrine:database:create
```
4. Execute the migration command:
```shell
php bin/console doctrine:migrations:migrate
```

## Console Command

A console command is available to fetch currency exchange rates and store them in the database and Redis.

To use the command, run the following:

```shell
php bin/console app:currency:rates EUR GBP USD COP
```
This command fetches the exchange rates for currencies like (EUR, GBP, USD, COP) from the CurrencyFreaks API. The rates are then saved in the MySQL database with EUR as the base currency and stored in Redis. For more information about the API, please visit the following link: https://currencyfreaks.com/documentation.html

To set the command as a daily cron job to run at 1am, use the following command:
```shell
php bin/console app:generate-cron-job EUR GBP USD
```

## API Endpoint

Execute the server with the next command:

```shell
symfony server:start
```

An endpoint is available to retrieve the exchange rates for a given set of currencies.
To use the endpoint, make a GET request to the following URL:
http://localhost:8000/api/exchange-rates?base_currency=EUR&target_currencies=USD,GBP,COP,CAD

The endpoint first checks Redis for the requested rates. If the rates are not in Redis, it fetches them from the MySQL database, stores them in Redis, and returns the rates. If the rates are in Redis, it returns the rates directly from Redis.

## Unit Tests
This project includes unit tests to verify the functionality of the console command and the API endpoint.

To execute the unit tests, run the following command:
```shell
php bin/phpunit
```

The FetchExchangeRatesCommand test ensures that the command fetches exchange rates, saves them in the database, and stores them in Redis. The output message is checked for the expected success message.
The ExchangeRateController test ensures that the controller returns a 200 OK status code and includes the expected target currencies in the response.

## Note
Remember to execute composer update to ensure you have the latest versions of the project's dependencies.

Please note that you may need to adjust the instructions and URLs based on your specific project configuration and requirements.

