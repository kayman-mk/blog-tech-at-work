---
comments_id: 82
date: 2022-11-02
title: Renovate Configuration
tags: renovate
---

It took us some time to figure out how to configure Renovate correctly. We wanted the following features to be enabled:

- a dedicated list of reviewers for all merge requests
- manual review of all major and Terraform minor updates
- ignore some files
- use our own Maven and Docker registries
- delay some package updates as they are updated too frequently
- group updates in one merge reuqest

With this features enabled, Renovate runs every hour during the business day (9 to 5) and creates merge requests for all
dependency updates. To trigger Renovate we use a scheduled pipeline in GitLab. We are still looking for an automation
to disable Renovate on public holidays.

## Dedicated list of reviewers

```javascript
module.exports = {
  reviewers: ['gitlab-user-id-1', 'gitlab-user-id-2']
};
```

Specify a list of reviewers for all merge requests. Use the GitLab user id. In case Renovate needs to assign a reviewer
(e.g. pipeline fails), it picks up one user from the list randomly.

## Manual review of all major updates and Terraform minor updates

```javascript
module.exports = {
    packageRules: [
        {
            matchUpdateTypes: ['patch', 'minor'],
            automerge: true
        },
        {
            matchDatasources: ['terraform-provider', 'terraform-module'],
            matchUpdateTypes: ['minor'],
            automerge: false
        }
    ]
};
```

Patches are always merged without review. Minor updated are also merged without review, except for Terraform changes.
We prefer to review them manually as there are often big changes in the Terraform providers. I suspect that we will
merge the Terraform module changes without a review in the future to reduce the workload.

## Ignore some files

```javascript
module.exports = {
    ignorePaths: ['**/modules/**/provider.tf']
};
```

In this case we skip all updates in the Terraform `provider.tf` files located somewhere in the `modules` directory. These
files define the minimum provider versions of a module which shouldn't be update automatically. They are changed by the
developer as soon as he uses a resource which requires a newer version of the provider.

## Use our own Maven and Docker registries

```javascript
module.exports = {
    hostRules: [
        {
            hostType: 'docker',
            matchHost: 'docker-registry.my-company.com',
            username: process.env.DOCKER_USERNAME,
            password: process.env.DOCKER_PASSWORD,
            insecureRegistry: false
        }
    ]
};
```

Sets the credentials to access `docker-registry.my-company.com`. The credentials are passed via environment variables to
Renovate and stored in GitLab CI/CD variables.

## Delay some package updates

```javascript
modules.exports = {
    packageRules: [
        {
            matchPackagePatterns: ['^software.amazon.*', '^com.amazonaws.*'],
            groupName: 'AWS packages',
            schedule: ['after 9:00am and before 11:59am on wednesday']
        }
    ]
};
```

Some packages are updated too frequently. This means that Renovate creates multiple merge requests for the same package
a day. We decided to reduce the number of merge requests by delaying the updates. The configuration above updates the
AWS packages only on Wednesday between 9 and 12 o'clock.

## Group updates in one merge request

```javascript
module.exports = {
    packageRules: [
        {
            matchDatasources: ['repology'],
            separateMajorMinor: false,
            groupName: 'repology packages',
        }
    ]
};
```

Several Docker images are updated via the Repology datasource to update the Alpine dependencies. As all dependencies have to
be updated at the same time, we group them in one merge request.

## Whole configuration

Place this code in `config.js` to configure Renovate.

```javascript
module.exports = {
    extends: ['config:base', ':semanticCommitTypeAll(chore)'],
    reviewers: ['gitlab-user-id-1', 'gitlab-user-id-2']
    reviewersSampleSize: 1,
    baseBranches: ['master'],
    labels: ['patch', 'dependencies'],
    onboarding: false,
    gitAuthor: 'Renovate <renovate@hlag.com>',
    printConfig: false,
    gitLabIgnoreApprovals: true,
    platformAutomerge: true,
    rebaseWhen: 'behind-base-branch',
    ignorePaths: ['**/modules/**/provider.tf'],
    hostRules: [
        {
            hostType: 'docker',
            matchHost: 'docker-registry.my-company.com',
            username: process.env.DOCKER_USERNAME,
            password: process.env.DOCKER_PASSWORD,
            insecureRegistry: false
        }
    ],
    packageRules: [
        {
            matchUpdateTypes: ['patch', 'minor'],
            automerge: true
        },
        {
            matchDatasources: ['terraform-provider', 'terraform-module'],
            matchUpdateTypes: ['minor'],
            automerge: false
        },
        {
            matchPackagePatterns: ['^software.amazon.*', '^com.amazonaws.*'],
            groupName: 'AWS packages',
            schedule: ['after 9:00am and before 11:59am on wednesday']
        }
        {
            matchDatasources: ['repology'],
            separateMajorMinor: false,
            groupName: 'repology packages',
        }
    ]
};
```
