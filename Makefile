APP_CONTAINER_NAME = "webWMS-php8.1"
DB_CONTAINER_NAME = "webWMS-MariaDB10.5"

help: ## Display this help
	@printf "\nUsage:\n  make \033[36m<target>\033[0m\n\nTargets:\n"
	@awk -F ":.*##" '/^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-35s\033[0m %s\n", $$1, $$2 }' $(MAKEFILE_LIST) | sort -u

######################################################################
############################### Docker ###############################
######################################################################
webwms-build: ## Build container image
	@docker-compose build

up: ## Starts the full docker-compose stack
	@docker-compose up -d

stop: ## Stops the full docker-compose stack, but keeps containers
	@docker-compose stop

down: ## Drop all container instances for a fresh restart
	@docker-compose down -v

######################################################################
################################ App #################################
######################################################################
bash: ## Connect with app container
	@docker exec -it $(APP_CONTAINER_NAME) bash

composer-install: ## Composer install
	@docker exec -it $(APP_CONTAINER_NAME) composer install
	@make chown-www-data

composer-update: ## Composer update
	@docker exec -it $(APP_CONTAINER_NAME) composer update

clear-caches: ## Clear caches
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'bin/console cache:clear'

yarn-watch: ## Yarn watch
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'yarn watch'

generate-bundle: ## Generate bundles
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'bin/console webwms:generate:bundle'

######################################################################
############################## Database ##############################
######################################################################
backup:
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'cd /var/backup/mysql && ./backup-database.sh'

list-backup:
	@docker exec -it $(APP_CONTAINER_NAME) ls /var/backup/mysql

######################################################################
################################ Logs ################################
######################################################################
logs: ## Tail application container logs
	@docker logs -f $(APP_CONTAINER_NAME)

db-logs: ## Tail database container logs
	@docker logs -f $(DB_CONTAINER_NAME)

######################################################################
########################### Code Analysis ############################
######################################################################
phpstan: ## run code analyse for src and bundles folder (phpstan)
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/bin/phpstan analyse';

phpstan-baseline: ## Run code analyse (phpstan) incl. baseline
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/bin/phpstan analyse --generate-baseline';

var-dump-check: ## Find var_dump, dd, etc.
	@docker exec -t $(APP_CONTAINER_NAME) vendor/bin/var-dump-check --symfony --doctrine --exclude vendor .

######################################################################
########################## Code Style Check ##########################
######################################################################
php-cs: ## run code style check
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/bin/php-cs-fixer fix --dry-run --using-cache=no \
	 -vvv --show-progress=dots --allow-risky=yes';

phpcs-fix: ## run code style fix
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/bin/php-cs-fixer fix --using-cache=no \
	 -vvv --show-progress=dots --allow-risky=yes';

phpmd: ## run code check (phpmd)
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/bin/phpmd './src/,./bundles/,./tests/' ansi rulesets.xml';

phpqa: ## run code check (phpmd)
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/edgedesign/phpqa/phpqa --analyzedDirs src';

######################################################################
############################# CSS Linter #############################
######################################################################
css-stylelint: ## Runs stylelint.
	@docker exec -t $(APP_CONTAINER_NAME) npm run stylelint-changed

css-stylelint-fix: ## Rus with --fix flag (Runs on the host system)
	@npm run stylelint-changed -- -r

######################################################################
############################# JS Linter ##############################
######################################################################
js-eslint: ## Runs ESLint.
	@docker exec -t $(APP_CONTAINER_NAME) npm run eslint-changed -- d

js-eslint-fix: ## Runs ESLint with --fix flag (Runs on the host system)
	@npm run eslint-changed -- -d public -r

######################################################################
############################### Tests ################################
######################################################################
#run-tests-all: ## run all tests
#	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/bin/codecept run';

run-tests-unit: ## run unit tests
	@docker exec -it $(APP_CONTAINER_NAME) bash -c './vendor/bin/phpunit --coverage-text';

#run-tests-api: ## run api tests
#	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/bin/codecept run api';
