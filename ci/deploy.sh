#!/bin/bash

set -Eeuo pipefail
shopt -s inherit_errexit

rm -rf ${ENVIRONMENT_DIR}/
mkdir ${ENVIRONMENT_DIR}

cd ${ENVIRONMENT_DIR}
git clone -b ${BITBUCKET_BRANCH} https://${BITBUCKET_USER}@bitbucket.org/softdev-nord/webwms.git

source after-build-deploy.sh