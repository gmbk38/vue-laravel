NAME = master
COMPOSE_YML = ../docker-compose/testing/docker-compose.yml

COMMIT_HASH = $(shell git rev-parse --verify --short=8 HEAD)

.PHONY: build push apply restart log bash

build:
	docker build -t hh.example/$(NAME) .
	git status -s >/dev/null && \
	git diff-index --quiet HEAD -- && \
	docker tag hh.example/$(NAME) hh.example/$(NAME):$(COMMIT_HASH) ||:

push:
	docker tag hh.example/$(NAME):$(COMMIT_HASH) hh.example.ru:5000/$(NAME):$(COMMIT_HASH)
	docker push hh.example.ru:5000/$(NAME):$(COMMIT_HASH)
	docker tag hh.example/$(NAME):$(COMMIT_HASH) hh.example.ru:5000/$(NAME):latest
	docker push hh.example.ru:5000/$(NAME):latest

apply: build
	docker compose -f $(COMPOSE_YML) up -d master
	sleep 10
	docker compose -f $(COMPOSE_YML) logs -f master ||:

restart:
	docker compose -f $(COMPOSE_YML) restart master
	sleep 10
	docker compose -f $(COMPOSE_YML) logs -f master ||:

log:
	docker compose -f $(COMPOSE_YML) logs -f master ||:

bash:
	docker compose -f $(COMPOSE_YML) exec master /bin/bash
