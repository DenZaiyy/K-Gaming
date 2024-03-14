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
	php bin/console doctrine:fixtures:load --group=test -n --env=test

coverage:
	php bin/phpunit --coverage-html ./coverage

clear:
	php bin/console c:c --env=test

.PHONY: fixtures-dev
fixtures-dev:
	php bin/console doctrine:fixtures:load --group=dev -n --env=dev

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
install: vendor/autoload.php
	/opt/alt/alt-nodejs20/root/usr/bin/npm install
	/opt/cpanel/composer/bin/composer install --no-dev --optimize-autoloader
	touch vendor/autoload.php

env:	
	cp .env.dist .env.dev.local
	cp .env.dist .env.test.local

.PHONY: deploy

deploy:
	ssh -A o2switch 'cd sites/dev.k-gaming.k-grischko.fr && git pull origin develop && make install'