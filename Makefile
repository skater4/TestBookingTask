.PHONY: up down build serve migrate clearcache

up:
	docker-compose config -q && \
	docker-compose up -d --force-recreate

down:
	docker-compose config -q && \
	docker-compose down --remove-orphans

reboot:
	make down && make up

build:
	docker-compose config -q && \
	docker-compose build --pull

rebuild:
	make down && make build && make up

composer-install:
	docker-compose config -q && \
	docker-compose exec laravel composer install

composer-update:
	docker-compose config -q && \
	docker-compose exec laravel composer update

composer-dump:
	docker-compose config -q && \
	docker-compose exec laravel composer dump-autoload -o

ide-helper:
	docker-compose config -q && \
	docker-compose exec laravel php artisan ide-helper:generate

ide-helper-models:
	docker-compose config -q && \
	docker-compose exec laravel php artisan ide-helper:models

generate-key:
	docker-compose config -q && \
	docker-compose exec laravel php artisan key:generate

migrate:
	docker-compose config -q && \
	docker-compose exec laravel php artisan migrate

seed:
	docker-compose config -q && \
	docker-compose exec laravel php artisan db:seed

test:
	docker-compose config -q && \
	docker-compose exec laravel php artisan test

npm-install:
	docker-compose config -q && \
	docker-compose run --rm node npm i && \
	docker-compose run --rm node npm install dotenv && \
	docker-compose run --rm node npm install jquery && \
	docker-compose run --rm node npm run build

npm-watch:
	docker-compose config -q && \
	docker-compose run --rm node npm run watch

webpack:
	docker-compose config -q && \
	docker-compose run --rm node npm run dev

clearcache:
	docker-compose exec laravel php artisan cache:clear; \
	docker-compose exec laravel php artisan clear-compiled; \
	docker-compose exec laravel php artisan config:clear; \
	docker-compose exec laravel php artisan route:clear; \
	docker-compose exec laravel php artisan view:clear; \
	docker-compose exec laravel php artisan optimize; \
	true

run-queue:
	docker-compose exec laravel pkill -9 -f queue; \
	docker-compose exec laravel php /var/www/html/artisan queue:listen --queue=highest,high,medium,low --timeout=300; \
	true;
