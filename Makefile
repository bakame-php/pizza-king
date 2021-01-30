# make install
install:
	@docker run --rm -it -v$(PWD):/app composer install

# make tests
tests:
	@docker run --rm -it -v$(PWD):/app --workdir=/app php:8.0-cli-alpine vendor/bin/phpunit

# coding style fix
phpcs:
	@docker run --rm -it -v$(PWD):/app --workdir=/app php:8.0-cli-alpine vendor/bin/php-cs-fixer fix -vvv --allow-risky=yes

# make order
order:
	@docker run --rm -it -v$(PWD):/app --workdir=/app php:8.0-cli-alpine php order.php

.PHONY: install tests phpcs order
