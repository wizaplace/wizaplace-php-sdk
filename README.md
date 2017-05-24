# Wizaplace's PHP SDK

## Install

```
composer require wizaplace/sdk
```

## Running linters and tests

### With Docker

```bash
docker build -t wizaplace-php-sdk-env .
docker run --rm -v $(pwd):/app -v ~/.cache/composer:/composer/cache -u $(id -u):$(id -g) --network=host wizaplace-php-sdk-env
```

### Without Docker

Requires Make, Composer, and all dependencies defined in [`composer.json`](/composer.json).

```bash
make all
```
