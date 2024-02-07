#!/bin/bash
# Helper script for creating environment variables file.
set -Eeuo pipefail
shopt -s inherit_errexit

# Define variables
export APP_ENV=prod
export APP_SECRET=af2b45ff237087d068938bb0858bddff
export APP_DEBUG=true

# Generate environment variables file
envsubst < .env.template > .env
