.PHONY: tests
tests:
	make prepare-test
	make fixtures-test
	make clear

prepare-test:
	php bin/console doctrine:database:drop --if-exists --force --env=test
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:schema:update --force --complete --env=test
	
fixtures-test:
	php bin/console doctrine:fixtures:load -n --env=test

clear:
	php bin/console c:c --env=test

.PHONY: analyze
analyze : 
	make analyze-dev
analyze-dev:
	npm audit
	composer valid
	php bin/console doctrine:schema:valid --skip-sync
	./vendor/bin/phpcs -p src
	./vendor/bin/phpcbf -p src
	.vendor/bin/phpstan src

.PHONY: install
install:
	npm install
	composer install

env:	
	cp .env.dist .env.dev.local
	cp .env.dist .env.test.local