---
comments_id: 86
date: 2022-11-30
title: Pipeline design for CI/CD
tags: ci, cd, GitLab, pipeline
---

The pipeline I propose here is optimized for Coninuous Deployment and runs very fast. Feature branches
are built once and the binary artifact is used for deployment. There is no rebuild in the `main` branch.

Basically the steps are:

1. Take the code and create the binary artifact (the feature branch).
2. Create a release to inform related parties about the change.
3. Deploy the binary artifact to your system.

## Feature Branch

The most complicated part of the pipeline as the code has to be compiled, tested and uploaded as a snapshot
version to a binary repository, e.g. Docker image, Maven artifact, ... This pipeline is especially performance
optimized and we strive for a runtime less than 5 minutes.

These are the single steps of the pipeline:

- linting
- compiling
- testing (unit/infrastructure)
- store artifact
- testing (API, EtE)
- allow manual deployment into a special environment

Always strive for the shortest possible runtime, e.g. build and store the artifact without waiting for the tests
in the step before will succeed. This way you can run the API and EtE tests almost in parallel with the unit test
which gains some performance and makes the developers happier.


## Release

The easiest part of the pipeline. Simply create the release and tag the source code with a version number. Use
a standard tool for this, e.g. semantic-release. It automatically creates the GitLab/GitHub release, the changelog,
notifications, ... and tags the `main` branch with a version number.

## Deployment
