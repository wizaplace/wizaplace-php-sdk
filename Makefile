

all: install lint stan test

install:
ifndef BUILD_ID
	composer install
else
	composer install --no-interaction --no-progress
endif

lint:
ifndef BUILD_ID
	./vendor/bin/phpcs
else
	./vendor/bin/phpcs --report-full --report-checkstyle=phpcs-checkstyle.xml
endif

stan:
ifndef BUILD_ID
	./vendor/bin/phpstan analyse -l 5 src tests
else
	./vendor/bin/phpstan --no-interaction --no-progress analyse --errorFormat=checkstyle -l 5 src tests | sed 's/<error/<error source="phpstan"/g' > phpstan-checkstyle.xml
endif

test:
ifndef BUILD_ID
	./vendor/bin/phpunit --configuration ./phpunit.xml
else
	php -dxdebug.coverage_enable=1 ./vendor/bin/phpunit --configuration ./phpunit.xml --log-junit ./phpunit-result.xml --coverage-clover ./clover.xml --coverage-html ./coverage/
endif

.PHONY: all install lint stan test
