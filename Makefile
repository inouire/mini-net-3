.PHONY: install clean run dependencies database locales dump_locales

install: dependencies database

update:
	composer update
	
clean:
	php bin/console cache:clear

run:
	php bin/console server:run

locales: dump_locales clean

dump_locales:
	curl --url "https://localise.biz/api/export/locale/fr.xlf?key=${LOCO_API_KEY}&format=symfony" --output "./app/Resources/translations/messages.fr.xlf"
	curl --url "https://localise.biz/api/export/locale/en.xlf?key=${LOCO_API_KEY}&format=symfony" --output "./app/Resources/translations/messages.en.xlf"

dependencies:
	composer install

database: data/mininet.db3

data/mininet.db3:
	php bin/console doctrine:database:create
	php bin/console doctrine:schema:update --force

	

