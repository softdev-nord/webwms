#!/bin/bash

set -Eeuo pipefail
shopt -s inherit_errexit

environment_dir=${ENVIRONMENT_DIR}

rm -rf ${environment_dir}/
mkdir ${environment_dir}

cd ${environment_dir}
git clone -b ${BITBUCKET_BRANCH} https://${BITBUCKET_USER}@bitbucket.org/softdev-nord/webwms.git

source after-build-deploy.sh