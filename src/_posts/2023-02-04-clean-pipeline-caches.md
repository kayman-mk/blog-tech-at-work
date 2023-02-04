---
comments_id: 110
title: "Clean your pipeline cache"
date: 2023-02-04
tags: pipeline, cache, clean, maven
---
Have you ever checked the size of your Maven, Terraform, Python, ... caches in your pipeline? I did it a couple of
days ago and was surprised. The Maven cache of nearly all projects grew to 1GB and more. This reduces the performance
of the pipeline as the cache has to be up-/downloaded and also unzipped/zipped every time a job runs. But how to
clean the cache?

## NodeJs

Nothing to do here as the `node_modules/` are not versioned in the cache. Thus make sure to generate a cache key
based on the `package-lock.json` file. Whenever you update the versions in this file a new cache is created.

```yaml
# GitLab pipeline
cache:
  key:
    files:
      - package-lock.json
  paths:
    - node_modules/
```

## Maven

They have a plugin available. Easy to use, but didn't remove everything from my cache. So I prefer the following
script:

```shell
# remove the whole cache
rm -rf .m2/repository/*

# downloads all artifacts according to pom.xml
mvn dependency:purge-local-repository -DsnapshotsOnly=false -DactTransitively=true
```

## Other caches

In case there are separate directories for each version:

1. Sort the directory names by version number
2. Delete everything but the latest version

Sorting by version number is not a trivial task. Checkout this script written by [andkirby](https://gist.github.com/andkirby):
[semversort.sh](https://gist.github.com/andkirby/54204328823febad9d34422427b1937b)