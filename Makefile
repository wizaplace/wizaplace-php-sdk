

all: install lint stan test

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
	./vendor/bin/coke --report-full --report-checkstyle=coke-checkstyle.xml
endif

stan:
ifndef BUILD_ID
	./vendor/bin/phpstan analyse -c phpstan.neon -l 4 src tests
else
	./vendor/bin/phpstan --no-interaction analyse -c phpstan.neon -l 4 src tests
endif

test:
ifndef BUILD_ID
	./vendor/bin/phpunit --configuration ./phpunit.xml
else
	php -dxdebug.coverage_enable=1 ./vendor/bin/phpunit --configuration ./phpunit.xml --log-junit ./phpunit-result.xml --coverage-clover ./clover.xml
endif

.PHONY: all install lint stan test
