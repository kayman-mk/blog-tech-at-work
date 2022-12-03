---
date: 2022-11-18
title: Extend the GitLab Runner with SSH clone
tags: GitLab, gitlab-runner, ssh
---

Our GitLab instance is hosted by a third party. Cloning via HTTPS is very unstable or not working at all. Nobody found out
what the problem is. But our team uses [GitLab Runner](https://https://gitlab.com/gitlab-org/gitlab-runner/) to operate 
he pipelines. Now, we have a problem: the GitLab Runner does not support cloning repositories via SSH. In July 2022 I
decided to create a [merge request](https://gitlab.com/gitlab-org/gitlab-runner/-/merge_requests/3518) and add this feature.

5 months later, the feature is still not available. But I found a reviewer who is dealing with it. Hooray!

## Configuration

Cloning via SSH might be as easy as cloning via HTTPS. Add the following to your `config.toml` and you are done:

```toml
[[runners]]
  clone_url  = "ssh://git@my.gitlab.domain"
```

## Where to get the image?

Either wait until the feature is available or (much better) use the forked version of the Gitlab Runner available
at [Hapag-Lloyd](https://gitlab.com/hapag-lloyd/gitlab-runner) project. I pushed several pre-build versions into
the [container registry](https://gitlab.com/hapag-lloyd/gitlab-runner/container_registry). If you need other versions of
the runner, feel free to create a merge request. The change is quite simple as you can see in other
[merge requests](https://gitlab.com/hapag-lloyd/gitlab-runner/-/merge_requests/5).

Also let me know if you need the runner for other architectures. I only built the runner for `linux/amd64`.
