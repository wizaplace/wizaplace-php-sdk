all: install lint test

install:
	composer install --dev --no-interaction --no-progress

lint:
	./vendor/bin/coke

test:
	./vendor/bin/phpunit --configuration ./phpunit.xml --log-junit ./phpunit-result.xml

.PHONY: all install lint test
