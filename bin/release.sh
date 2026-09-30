#!/usr/bin/env bash
# Usage: bin/release.sh 1.2.0
# Bumps the theme version, commits, tags v1.2.0 and pushes; GitHub Actions then builds the release.
set -euo pipefail
cd "$(dirname "$0")/.."

version="${1:?Usage: bin/release.sh X.Y.Z}"
[[ "$version" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]] || { echo "Version must look like 1.2.0"; exit 1; }
[ -z "$(git status --porcelain)" ] || { echo "Commit or stash your changes first."; exit 1; }
[ "$(git rev-parse --abbrev-ref HEAD)" = "master" ] || { echo "Release from master."; exit 1; }

# perl rather than sed -i, whose syntax differs between macOS and Linux.
perl -pi -e "s/^Version: .*/Version: $version/" style.css
perl -pi -e "s/^define\\( 'SK_THEME_VERSION', '.*' \\);/define( 'SK_THEME_VERSION', '$version' );/" functions.php

git add style.css functions.php
git commit -m "Release $version"
git tag "v$version"
git push origin master "v$version"
echo "Pushed v$version. The release appears at https://github.com/nicolasmatter/solarkraftwerk-theme/releases once the workflow finishes."
