#!/usr/bin/env sh
set -euo pipefail

cp -dpr /mnt/src/* .
jekyll serve -H 0.0.0.0