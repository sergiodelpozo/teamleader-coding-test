# vim: set ft=make ts=8 noet

# Variables
# UNAME		:= $(shell uname -s)

.EXPORT_ALL_VARIABLES:

.PHONY: help
help:	### this screen. Keep it first target to be default
ifeq ($(UNAME), Linux)
	@grep -P '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | \
		awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'
else
	@# this is not tested, but prepared in advance for you, Mac drivers
	@awk -F ':.*###' '$$0 ~ FS {printf "%15s%s\n", $$1 ":", $$2}' \
		$(MAKEFILE_LIST) | grep -v '@awk' | sort
endif

# Targets
#
.PHONY: debug
debug:	### Debug Makefile itself
	@echo $(UNAME)

.PHONY: composer
composer:
	@docker-compose run --rm api composer $(filter-out $@,$(MAKECMDGOALS))

.PHONY: start
start:
	@docker-compose up -d

.PHONY: tests
tests:
	@docker-compose run --rm api vendor/bin/phpunit --testsuite Unit $(filter-out $@,$(MAKECMDGOALS))

.PHONY: tests-integration
tests-integration:
	$(call integration_tests)

.PHONY: tests-all
tests-all:
	$(call all_tests)

.PHONY: init
init: ### Bootstrap the project
	$(call init_project, $(ARGS))


define init_project
	@docker network create ext-teamleader-discounts-network || true
	@docker-compose up -d
	@docker-compose run --rm api composer install
	@docker-compose run --rm api vendor/bin/phinx migrate
	@docker-compose run --rm api vendor/bin/phinx seed:run
endef

define integration_tests
	@docker-compose run -e APP_ENV=testing --rm api vendor/bin/phinx migrate
	@docker-compose run --rm api vendor/bin/phpunit --testsuite Integration
endef

define all_tests
	@docker-compose run -e APP_ENV=testing --rm api vendor/bin/phinx migrate
	@docker-compose run --rm api vendor/bin/phpunit
endef
