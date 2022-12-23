---
date: 2022-12-03
title: "Linting Dockerfiles"
tags: lint dockerfile
---
Linting your Dockerfiles is always a good idea. The linter finds common errors, style problems and can enforce
best practices. It will make life easier for your developers.

The best option is to integrate the linter into your CI/CD pipeline to enforce the rules for everyone. Also check
for plugins availale for your preferred IDE. This way you get a quick feedback during coding.

## Which tool to use?

There are several tools available. Years ago I decided to go with [Hadolint](https://github.com/hadolint/hadolint). It
also provides a check for your `RUN` commands and enforces best practices like "DL3009 - Delete the apt-get lists after
installing something.". Also check if you find a plugin for your IDE.

Other tools are

- [Dockerlint](https://github.com/RedCoolBeans/dockerlint), but seems to be unmaintained for years now
- [Dockle](https://github.com/goodwithtech/dockle), it's not a linter only

## How to lint a Dockerfile

This is quite easy and can be achieved with a single line of code: `docker run --rm -i hadolint/hadolint < Dockerfile`

## Can I ignore rules?

Yes you can, but you shouldn't. The rules exist for a reason so don't ignore them. In very special cases you
can annotate the line before the violation.

```Dockerfile
# hadolint ignore=DL3009`
RUN apt-get update && apt-get install -y curl
```
