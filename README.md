# Wizaplace's PHP SDK

## Install

```
composer require wizaplace/sdk
```

## Usage

Here is a small example:

```php
$factory = \Wizaplace\ServicesFactory::fromApiBaseUrl("http://wizaplace.loc/api/v1/");
$catalogService = $factory->catalogService();
var_dump($catalogService->search());
```

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
