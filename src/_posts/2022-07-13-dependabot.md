---
date: 2022-07-13
title: Updating dependencies with Dependabot
---
# Update your software dependencies on a regular interval

## What is it good for?

Dependabot is a tool which updates all your dependencies in a repository on a regular basis. It is doing so by creating pull
requests, so it follows your defined release process. It behaves like a developer who does the boring work for your team. Great!

All major package ecosystems are supported (e.g. NPM, Maven, Docker, NuGet) and the update frequency can be configured.
This is especially useful for packages which are frequently updated (e.g. AWS SDK).

We update the dependencies of all our repositories on an hourly interval from 9am to 4pm. The pull requests are
automatically merged into production without a manual review. But we made one exception to the rule: Major updates have
to be reviewed by a team member. Packages which are frequently updated (see above) are updated once a week only.

You can run the tool for free so just give it a try!

## Why updating dependencies?

I will not repeat all the pros and cons (are there any?) here. Instead, use the following articles for a good overview:

- [Why and How You Should Automate Dependency Updates](https://www.mend.io/free-developer-tools/blog/why-and-how-you-should-automate-dependency-updates/)
- [Continuous dependency updates: Improving processes by front-loading pain](https://snyk.io/blog/continuous-dependency-updates-improving-processes-front-loading-pain/)
- [Automatic dependencies updates for frontend services](https://medium.com/azimolabs/automatic-dependencies-updates-for-frontend-services-3af5873d5592)

## Any other tools?

I know at least [Renovate](https://www.mend.io/free-developer-tools/renovate/) which does the same thing and in the meantime
they added a GitHub action, so it integrates into GitHub as well as Dependabot does.

I am using this tool for a couple of years now to keep the dependencies of ~100 GitLab repositories up to date and
I don't see any major difference.

## Sample Configuration

```yaml
---
version: 2
updates:
  - package-ecosystem: "github-actions"
    directory: "/.github/workflows"
    schedule:
      interval: "weekly"
    commit-message:
      prefix: "ci"

  - package-ecosystem: "bundler"
    directory: "/src"
    schedule:
      interval: "weekly"
    commit-message:
      prefix: "chore(deps)"

  - package-ecosystem: "docker"
    directory: "/"
    schedule:
      interval: "weekly"
    commit-message:
      prefix: "chore(deps)"
```

Place the above configuration in `.github/dependabot.yml` in your repository. As soon as you merge it into the default branch,
Dependabot starts to create pull requests. The configuration is straight forward as you can see. But to support semantic
versioning, I configured the commit message prefix.
