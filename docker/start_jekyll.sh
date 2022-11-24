#!/usr/bin/env sh
set -euo pipefail

cp -dpr /mnt/src/* .
JEKYLL_ENV=production jekyll serve -H 0.0.0.0