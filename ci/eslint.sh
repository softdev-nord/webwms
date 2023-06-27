#!/bin/bash
# Helper script to run ESLint that differ from the master.

set -Eeuo pipefail
shopt -s inherit_errexit
readonly BASE_PATH="$( cd "$(dirname "$0")" ; pwd -P )"
source "${BASE_PATH}/config.sh"

DEFAULT_SUB_FOLDER="./"
IFS='
'

# defaults
format="stylish"
output_file=""
sub_folder="${DEFAULT_SUB_FOLDER}"
is_repair=""

USAGE="
Usage:
$0 [-h] [-d <lint-dir>] [-f <format>] [-o <output-file>]

Options:
-h            Show this message.
-d <dir>      Restrict linting to a sub folder
-f <format>   This option specifies the output format for the console (default: ${format})
-o <out-file> Specify file to write report to
-r            Repair code-style issues.
"

help() {
  echo "$USAGE"
}

while getopts 'hrd:f:o:' c; do
  case ${c} in
    h) help; exit 0 ;;
    d) sub_folder="${OPTARG}" ;;
    f) format="${OPTARG}" ;;
    o) output_file="${OPTARG}" ;;
    r) is_repair="--fix" ;;
    *) help; exit 1 ;;
  esac
done


# main
changed_files=$(${BASE_PATH}/get-changeset.sh | grep -E "\.js$") || echo

# TODO: enable these once the first CI PR is merged.
#
#if echo "${changed_files}" | grep -qE ".eslintrc.js$"; then
#  # If the ESLint config changes, we're running the tests on all files
#  echo "Config change detected (.eslintrc.js), checking all JS files (in ${sub_folder}) .."
#  changed_files="${sub_folder}"
#fi
if [[ "${sub_folder}" != "${DEFAULT_SUB_FOLDER}" ]]; then
  # limit files to a sub folder
  changed_files=$(echo "$changed_files" | grep -E "^${sub_folder}") || echo
fi

if [[ -z "${changed_files}" ]]; then
  echo "No changed JS files. Skipping check."
  exit 0
fi

#cmd="node_modules/.bin/eslint --config .eslintrc.js --resolve-plugins-relative-to ./node_modules --no-error-on-unmatched-pattern --format ${format} ${is_repair} --no-ignore"
cmd="node_modules/.bin/eslint --config .eslintrc.js --resolve-plugins-relative-to ./node_modules --no-error-on-unmatched-pattern --format ${format} ${is_repair}"
if [[ "${output_file}" != "" ]]; then
  cmd="${cmd}  --output-file ${output_file}"
fi

cmd="${cmd} $(echo ${changed_files} | xargs)"

echo "${cmd}"
bash -c ${cmd}
