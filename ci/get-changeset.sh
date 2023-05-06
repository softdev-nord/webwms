#!/bin/bash
# Calculate the changeset of the current branch or PR.
set -Eeuo pipefail
shopt -s inherit_errexit

USAGE="This script calculates the git changeset.

Usage:
$0 [-v] [-h]

Options:
-h      Show this message.
-v      Enable verbose output
"

help() {
  echo "$USAGE"
}

log() {
  [[ ${verbosity} -lt 1 ]] && return 0
  local msg="$1"
  echo "[DEBUG] ${msg}"
}

set_is_ci() {
  # Sets "is_ci" depending if we're running within the real CI or locally
  [[ -z "${CI+x}" ]] && CI=false
  [[ "${CI}" == "true" ]] && is_ci=1
  log "Running in CI? ${is_ci}"
  return 0
}

set_is_pull_request() {
  [[ -z "${BITBUCKET_PR_DESTINATION_BRANCH+x}" ]] && BITBUCKET_PR_DESTINATION_BRANCH=""
  [[ -n "${BITBUCKET_PR_DESTINATION_BRANCH}" ]] && is_pull_request=1
  log "Triggered by PR? ${is_pull_request}"
  return 0
}

fetch_remote_branch() {
  local branch="${1}"
  if ! git show-ref --verify --quiet "refs/heads/${branch}"; then
    log "Branch '${branch}' not found locally, fetching .."
    git fetch origin "refs/heads/${branch}:refs/remotes/origin/${branch}" > /dev/null
    git branch "${branch}" "origin/${branch}" > /dev/null
  fi
}

calculate_changeset() {

  if [[ ${is_pull_request} -eq 1 ]]; then
    # @TODO: this is only set, when using "pullrequest" in pipeline
    # @TODO: not implemented yet
    # When running for PR, we know the destination branch to compare against
    compare_branch="${BITBUCKET_PR_DESTINATION_BRANCH}"
    log "Calculating changeset for PR (against ${compare_branch})"
  else
    # When running for normal commit, we need to check against master
    compare_branch="development"
  fi

  fetch_remote_branch "${compare_branch}"

  # https://git-scm.com/book/en/v2/Git-Tools-Revision-Selection
  git_diff_path="${compare_branch}..HEAD"
  log "Calculating git changeset (${git_diff_path}) .."
  log "--- Changed files ------------------------------------------------------"
  git diff --diff-filter=d --name-only "${git_diff_path}"
  # --diff-filter=ACMRTUXB @TODO: needed?

  if [[ ${is_ci} -eq 0 ]]; then
    # When running locally, we're adding the uncommitted changes from workspace
    log "--- Uncommitted files ------------------------------------------------"
    git diff --name-only
  fi

}


# init
verbosity=0
is_ci=0
is_pull_request=0

while getopts 'hv' c; do
  case ${c} in
    h) help; exit 0 ;;
    v) verbosity=1 ;;
    *) help; exit 1 ;;
  esac
done

# main
set_is_ci
set_is_pull_request

calculate_changeset is_pull_request
