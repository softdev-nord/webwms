#!/bin/bash

set -Eeuo pipefail
shopt -s inherit_errexit

rm -rf ${ENVIRONMENT_DIR}/
mkdir ${ENVIRONMENT_DIR}

cd ${ENVIRONMENT_DIR}
git clone -b ${BITBUCKET_BRANCH} https://${BITBUCKET_USER}:${BITBUCKET_PASSWORD}@bitbucket.org/softdev-nord/webwms.git .

/opt/plesk/php/8.1/bin/php /usr/lib/plesk-9.0/composer.phar install --ignore-platform-reqs --no-scripts && \
/opt/plesk/php/8.1/bin/php bin/console cache:clear

/opt/plesk/node/16/bin/yarn watch