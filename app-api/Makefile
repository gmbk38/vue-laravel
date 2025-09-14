NAME = app-api
COMPOSE_YML = ../../docker-compose/testing/docker-compose.yml

include ../docker.mk

.PHONY: apply restart log bash

apply: build
	docker compose -f $(COMPOSE_YML) up -d app-api
	sleep 10
	docker compose -f $(COMPOSE_YML) exec app-api tail -n 100 -f /var/log/php8.3-fpm.log ||:

restart:
	docker compose -f $(COMPOSE_YML) restart app-api
	sleep 10
	docker compose -f $(COMPOSE_YML) exec app-api tail -n 100 -f /var/log/php8.3-fpm.log ||:

log:
	docker compose -f $(COMPOSE_YML) exec app-api tail -n 100 -f /var/log/php8.3-fpm.log ||:

bash:
	docker compose -f $(COMPOSE_YML) exec app-api /bin/bash
