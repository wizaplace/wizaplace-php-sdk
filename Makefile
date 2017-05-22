all: install lint test

install:
	composer install

lint:
	./vendor/bin/coke

test:
	./vendor/bin/phpunit --configuration ./phpunit.xml

.PHONY: all install lint test
