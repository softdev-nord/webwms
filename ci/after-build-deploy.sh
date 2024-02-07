#!/bin/bash

set -Eeuo pipefail
shopt -s inherit_errexit

cd ${ENVIRONMENT_DIR}

/opt/plesk/php/8.1/bin/php /usr/lib/plesk-9.0/composer.phar install --ignore-platform-reqs --no-scripts && \
/opt/plesk/php/8.1/bin/php bin/console cache:clear
