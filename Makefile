.PHONY: install clean run dependencies database

install: dependencies database

update:
	composer update
	
clean:
	php bin/console cache:clear

run:
	php bin/console server:run

dependencies:
	composer install

database: data/mininet.db3

data/mininet.db3:
	php bin/console doctrine:database:create
	php bin/console doctrine:schema:update --force

	

