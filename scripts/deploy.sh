#!/bin/sh
# Run on the server instead of plain `git pull`.
# Fetches commits and tags, merges, then regenerates .version.
set -e
root=$(git rev-parse --show-toplevel)
git fetch --tags
git merge
git describe --tags --always > "$root/.version"