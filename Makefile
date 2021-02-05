# make install
install:
	@docker run --rm -it -v$(PWD):/app composer install

# tests
tests:
	@docker run --rm -it -v$(PWD):/app --workdir=/app php:8.0-cli-alpine vendor/bin/phpunit

# psalm
psalm:
	@docker run --rm -it -v$(PWD):/app --workdir=/app php:8.0-cli-alpine vendor/bin/psalm --show-info=true

# phpstan
phpstan:
	@docker run --rm -it -v$(PWD):/app --workdir=/app php:8.0-cli-alpine vendor/bin/phpstan analyse -l max -c phpstan.neon src --ansi

# coding style fix
phpcs:
	@docker run --rm -it -v$(PWD):/app --workdir=/app php:8.0-cli-alpine vendor/bin/php-cs-fixer fix -vvv --allow-risky=yes

# phpmd
phpmd:
	@docker run --rm -it -v$(PWD):/app --workdir=/app php:8.0-cli-alpine vendor/bin/phpmd src text phpmd.xml --exclude=vendor --ansi

# make website
rest:
	@docker run --rm -it -v$(PWD):/app --workdir=/app -p4000:4000 php:8.0-cli-alpine php -S 0.0.0.0:4000 -t public

.PHONY: install tests psalm phpstan phpcs phpmd order
