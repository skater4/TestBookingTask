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

generate:
	docker compose exec laravel php artisan ide-helper:generate && \
	docker compose exec laravel php artisan ide-helper:meta && \
	docker compose exec laravel php artisan ide-helper:models -M

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

clearcache:
	docker-compose exec laravel php artisan cache:clear; \
	docker-compose exec laravel php artisan clear-compiled; \
	docker-compose exec laravel php artisan config:clear; \
	docker-compose exec laravel php artisan route:clear; \
	docker-compose exec laravel php artisan view:clear; \
	docker-compose exec laravel php artisan optimize; \
	true

pint:
	docker compose config -q && \
	docker compose exec laravel sh pint.sh

phpstan:
	docker compose config -q && \
	docker compose exec laravel sh phpstan.sh