

all: install lint test

install:
ifndef BUILD_ID
	composer install
else
	composer install --no-interaction --no-progress
endif

lint:
ifndef BUILD_ID
	./vendor/bin/coke
else
	./vendor/bin/coke --report-junit=coke-result.xml
endif


test:
ifndef BUILD_ID
	./vendor/bin/phpunit --configuration ./phpunit.xml
else
	./vendor/bin/phpunit --configuration ./phpunit.xml --log-junit ./phpunit-result.xml
endif

.PHONY: all install lint test
