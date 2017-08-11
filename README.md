PHP SDK for the Wizaplace e-commerce API: https://sandbox.wizaplace.com/api/v1/doc/

## Install

```
composer require wizaplace/sdk
```

## Usage

You can find some small examples there: [Wizaplace\Tests\ExampleTest](./tests/ExampleTest.php)

## Running linters and tests

### With Docker

```bash
./docker-make all
```

### Without Docker

Requires Make, Composer, and all dependencies defined in [`composer.json`](/composer.json).

```bash
make all
```

## `php-vcr` behaviour

Any changes to an API call in a tested method will make regenerate the cassette files (this needs the tests to be run twice). Those changes need to be committed alongside your code.
