#!/bin/bash

set -Eeuo pipefail
shopt -s inherit_errexit

environment_dir=${ENVIRONMENT_DIR}

rm -rf ${ENVIRONMENT_DIR}/
mkdir ${ENVIRONMENT_DIR}

cd ${ENVIRONMENT_DIR}
git clone -b ${BITBUCKET_BRANCH} https://${BITBUCKET_USER}:${BITBUCKET_PASSWORD}@bitbucket.org/softdev-nord/webwms.git

source after-build-deploy.sh