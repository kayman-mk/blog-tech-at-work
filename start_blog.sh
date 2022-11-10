#!/usr/bin/env bash
set -euo pipefail

# convert the path into a Windows like path
pwd=$(pwd)
windows_pwd=$(echo "$pwd" | sed "s/\//\\\\\\\\/g")

docker build -t jekyll_blog -f docker/Dockerfile.jekyll .

winpty docker run --rm -it -v "c:${windows_pwd:3}:/mnt" -p 4000:4000 jekyll_blog