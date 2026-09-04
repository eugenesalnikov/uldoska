.PHONY: up down restart build rebuild

help:
	@echo "Available commands:"
	@echo "  make up       Start containers"
	@echo "  make down     Stop and delete containers"
	@echo "  make restart  Only build images"
	@echo "  make build    Re-build images"
	@echo "  make rebuild  Re-build images and start containers"

COMPOSE = docker compose -f docker-compose.yaml
COMPOSE_PROXY = $(COMPOSE) -f docker-compose.proxy.yaml
COMPOSE_LOCAL = $(COMPOSE) -f docker-compose.local.yaml

up:
	$(COMPOSE_PROXY) up -d

down:
	$(COMPOSE_PROXY) down

restart:
	$(COMPOSE_PROXY) down
	$(COMPOSE_PROXY) up -d

build:
	$(COMPOSE_PROXY) build

rebuild:
	$(COMPOSE_PROXY) up -d --build
