APP_CONTAINER_NAME = "webwms-php8.3"
DB_CONTAINER_NAME = "webwms-MariaDB10.5"

help: ## Display this help
	@printf "\nUsage:\n  make \033[36m<target>\033[0m\n\nTargets:\n"
	@awk -F ":.*##" '/^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-35s\033[0m %s\n", $$1, $$2 }' $(MAKEFILE_LIST) | sort -u

######################################################################
############################### Docker ###############################
######################################################################
build: ## Build container image
	@docker-compose build

up: ## Starts the full docker-compose stack
	@docker-compose up -d --build

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
phpstan: ## Run code analyse for src and bundles folder (phpstan)
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/bin/phpstan analyse';

phpstan-baseline: ## Run code analyse (phpstan) incl. baseline
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/bin/phpstan analyse --generate-baseline';

var-dump-check: ## Find var_dump, dd, etc.
	@docker exec -t $(APP_CONTAINER_NAME) vendor/bin/var-dump-check --symfony --doctrine --exclude vendor .

php-compatibility-check:
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'composer sniffer:php83';

######################################################################
########################## Code Style Check ##########################
######################################################################
php-cs: ## Run code style check
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/bin/php-cs-fixer fix --dry-run --using-cache=no \
	 -vvv --show-progress=dots --allow-risky=yes';

phpcs-fix: ## Run code style fix
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/bin/php-cs-fixer fix --using-cache=no \
	 -vvv --show-progress=dots --allow-risky=yes';

phpmd: ## Run code check (phpmd)
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/bin/phpmd './src/,./module/,./tests/' ansi rulesets.xml';

phpqa: ## Run code check (phpmd)
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/edgedesign/phpqa/phpqa --analyzedDirs src --execution no-parallel';

######################################################################
############################ Twig Linter #############################
######################################################################
twig-lint: ## Run twig lint.
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'bin/console lint:twig templates';

######################################################################
############################# CSS Linter #############################
######################################################################
css-stylelint: ## Run stylelint.
	@docker exec -t $(APP_CONTAINER_NAME) npm run stylelint-changed

css-stylelint-fix: ## Run with --fix flag (Runs on the host system)
	@npm run stylelint-changed -- -r

######################################################################
############################# JS Linter ##############################
######################################################################
js-eslint: ## Run ESLint.
	@docker exec -t $(APP_CONTAINER_NAME) npm run eslint-changed -- d

js-eslint-fix: ## Run ESLint with --fix flag (Runs on the host system)
	@npm run eslint-changed -- -d public -r

######################################################################
############################ Yaml Linter #############################
######################################################################
yaml-lint: ## Lints the yaml files (config folder)
	@docker exec -t $(APP_CONTAINER_NAME) bin/console lint:yaml config --parse-tags

######################################################################
############################### Tests ################################
######################################################################
run-tests-unit: ## Run unit tests
	@docker exec -it $(APP_CONTAINER_NAME) bash -c './vendor/bin/phpunit --testdox --colors=always --coverage-html var/reports/ ';

######################################################################
##################### Automated Code Refactoring #####################
######################################################################
run-rector: ## Run automated refactoring dry run
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/bin/rector process --dry-run';

run-rector-refactoring: ## Run automated refactoring
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/bin/rector process';

report-metrics: ## Run the phpmetrics report
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/bin/phpmetrics --config=php_metrics_config.yml';

######################################################################
#################### Developer Information Tools #####################
######################################################################
find-leaking-classes: ## Find leaking classes that you never use... and get rid of them. | https://github.com/TomasVotruba/class-leak
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/bin/class-leak check bin src';

lines-of-code: ## Run the lines of code command | https://github.com/TomasVotruba/lines
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/bin/lines measure src';

composer-unused: ## Show unused composer dependencies by scanning code | https://github.com/TomasVotruba/composer-unused
	@docker exec -it $(APP_CONTAINER_NAME) bash -c 'vendor/bin/composer-unused';