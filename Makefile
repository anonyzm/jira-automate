env:
	cp .env.dist .env
	
compose-override:
	cp docker-compose.override.yml.dist docker-compose.override.yml

up:
	docker compose up -d

down:
	docker compose stop

recreate: 
	docker compose up -d --force-recreate

recreate-app: 
	docker compose up -d --force-recreate app

ps:
	docker compose ps

bash:
	docker compose exec --user application app bash

bash-root:
	docker compose exec app bash

build:
	docker compose build

build-app:
	docker compose build app

rebuild:
	docker compose down
	docker compose build --no-cache
	docker compose up -d

logs:
	docker compose logs --tail 50 app

migrate:
	docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction

migrate-status:
	docker compose exec app php bin/console doctrine:migrations:status

prune:
	docker system prune -f
	docker system prune -a
	docker volume rm
	docker volume prune -f
