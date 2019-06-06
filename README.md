PHP SDK for the Wizaplace e-commerce API: https://sandbox.wizaplace.com/api/v1/doc/

## Installation

```
composer require wizaplace/sdk
```

## Usage

You can find some small examples there: [Wizaplace\SDK\Tests\ExampleTest](./tests/ExampleTest.php)

## Development

### Running linters and tests

#### With Docker

```bash
./docker-make all
```

#### Directly on your machine

Requires Make, Composer, and all dependencies defined in [`composer.json`](/composer.json).

```bash
make all
```

### `php-vcr` behaviour

Any changes to an API call in a tested method will make regenerate the cassette files (this needs the tests to be run twice). Those changes need to be committed alongside your code.

## Deployment

When `master` is ready to be deployed on Packagist.org:

- Go to the [releases](https://github.com/wizaplace/wizaplace-php-sdk/releases) page on Github
- Click on "Draft a new release"
- Choose a new tag following semver (e.g. `1.30.1`)
- Change the target to `master` 
- Set the release title to "Release {version}" (e.g. "Release 1.30.1")
- Copy the relevant section of the Changelog in the release description, or if the version is a patch, just write the list of fixes in a list
- Click on "Publish release"
- Check that the version is published on [Packagist](https://packagist.org/packages/wizaplace/sdk)
