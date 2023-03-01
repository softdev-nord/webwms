#!/usr/bin/env bash
# Shared configs for CI scripts
set -Eeuo pipefail
shopt -s inherit_errexit


# Working directory of the container image
export WORK_DIR="/var/www/html"
