# FinTrack — atalhos Docker
# Uso: make <comando>

CONTAINER=fintrack_app

## ─── Ambiente ────────────────────────────────────────────────────────────────

up:         ## Sobe todos os containers
	docker compose up -d

down:       ## Para todos os containers
	docker compose down

build:      ## Reconstrói a imagem da app
	docker compose build app

restart:    ## Reinicia os containers
	docker compose restart

logs:       ## Exibe logs em tempo real
	docker compose logs -f

ps:         ## Lista containers rodando
	docker compose ps

## ─── App ─────────────────────────────────────────────────────────────────────

setup:      ## Primeira configuração após clonar o projeto
	@if [ ! -f .env.docker ]; then cp .env.docker.example .env.docker; fi
	cp .env.docker .env
	docker compose up -d --build
	@echo "⏳ Aguardando PostgreSQL ficar pronto..."
	@until docker exec fintrack_postgres pg_isready -U fintrack > /dev/null 2>&1; do sleep 1; done
	@echo "✅ PostgreSQL pronto"
	docker exec $(CONTAINER) composer install
	docker exec $(CONTAINER) npm install
	docker exec $(CONTAINER) npm run build
	docker exec $(CONTAINER) php artisan key:generate
	docker exec $(CONTAINER) php artisan config:clear
	docker exec $(CONTAINER) php artisan migrate:fresh --seed
	@echo ""
	@echo "✅ FinTrack rodando em http://localhost:8080"

bash:       ## Abre bash dentro do container app
	docker exec -it $(CONTAINER) bash

artisan:    ## Roda artisan. Ex: make artisan CMD="route:list"
	docker exec $(CONTAINER) php artisan $(CMD)

migrate:    ## Roda as migrations
	docker exec $(CONTAINER) php artisan migrate

fresh:      ## Recria o banco e roda seeders
	docker exec $(CONTAINER) php artisan migrate:fresh --seed

seed:       ## Roda os seeders
	docker exec $(CONTAINER) php artisan db:seed

test-data:  ## Popula com dados de teste
	docker exec $(CONTAINER) php artisan db:seed --class=TestDataSeeder

tinker:     ## Abre o Tinker
	docker exec -it $(CONTAINER) php artisan tinker

test:       ## Roda php artisan test (SQLite em memória via phpunit.xml)
	@docker inspect -f '{{.State.Running}}' $(CONTAINER) 2>/dev/null | grep -q true || (echo "Erro: $(CONTAINER) não está rodando. Execute: make up  ou  make setup" && exit 1)
	docker exec $(CONTAINER) php artisan config:clear --ansi
	docker exec $(CONTAINER) php artisan route:clear --ansi
	docker exec $(CONTAINER) php artisan test

## ─── Assets ──────────────────────────────────────────────────────────────────

npm-dev:    ## Compila assets em watch mode
	docker exec $(CONTAINER) npm run dev

npm-build:  ## Compila assets para produção
	docker exec $(CONTAINER) npm run build

## ─── Qualidade ───────────────────────────────────────────────────────────────

pint:       ## Formata o código com Laravel Pint
	docker exec $(CONTAINER) ./vendor/bin/pint

pint-test:  ## Verifica formatação sem alterar
	docker exec $(CONTAINER) ./vendor/bin/pint --test

## ─── Banco ───────────────────────────────────────────────────────────────────

psql:       ## Abre o CLI do PostgreSQL
	docker exec -it fintrack_postgres psql -U fintrack -d fintrack

.PHONY: up down build restart logs ps setup bash artisan migrate fresh seed test-data tinker test npm-dev npm-build pint pint-test psql
