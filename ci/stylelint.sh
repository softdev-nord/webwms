#!/usr/bin/env bash
# Helper script to run ESLint that differ from the master.

set -Eeuo pipefail
shopt -s inherit_errexit
readonly BASE_PATH="$( cd "$(dirname "$0")" ; pwd -P )"
source "${BASE_PATH}/config.sh"

DEFAULT_PATTERN="public/assets/css/**/*.{css,less,sass,scss}"
IFS='
'

# defaults
format="stylish"
output_file=""
is_repair=""

USAGE="
Usage:
$0 [-h] [-f <format>] [-o <output-file>]

Options:
-h            Show this message.
-f <format>   This option specifies the output format for the console (default: ${format})
-o <out-file> Specify file to write report to
"

help() {
  echo "$USAGE"
}

while getopts 'hrd:f:o:' c; do
  case ${c} in
    h) help; exit 0 ;;
    f) format="${OPTARG}" ;;
    o) output_file="${OPTARG}" ;;
    r) is_repair="--fix" ;;
    *) help; exit 1 ;;
  esac
done


# main
changed_files=$("${BASE_PATH}/get-changeset.sh")
changed_style_files=$(printf '%s\n' ${changed_files} | grep -E '\.(css|less|sass|scss)$') || echo

# TODO: enable these once the first CI PR is merged.
#
#if echo "${changed_files}" | grep -qE "^\.stylelintrc.json$"; then
#  echo "Config change detected (.stylelintrc.json), checking all style files .."
#  changed_style_files="${DEFAULT_PATTERN}"
#else
  echo "Only checking changed style files .."
#fi

if [[ -z "${changed_style_files}" ]]; then
  echo "No files changed. Skipping stylelint."
  exit 0
fi

cmd="node_modules/.bin/stylelint --allow-empty-input --cache --cache-location .stylelintcache ${is_repair}"
if [[ "${format}" == "junit" ]]; then
  cmd="${cmd} --custom-formatter 'node_modules/stylelint-junit-formatter'"
fi

if [[ "${output_file}" != "" ]]; then
  cmd="${cmd} --output-file ${output_file}"
fi

cmd="${cmd} ${changed_style_files}"

echo ${cmd}
bash -c ${cmd}
