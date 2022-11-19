---
date: 2022-08-12
title: Be faster on the command line with GLab CLI
---
# Be faster on the command line: GitLab

## Motivation

We are using a self hosted GitLab solution to host all our repositories. For me it was always a pain to create merge
requests and push changes to the remote repository (already tackled in [my previous post](/2022/07/25/faster-commandline-git.html)).

This time I found the [GLab tool on GitHub](https://github.com/profclems/glab/) which is the remote control for GitLab. In the
meantime GitLab integrated it and offers it as the official [GitLab CLI tool](https://gitlab.com/gitlab-org/cli/). GitHub
has a similar tool called [gh](https://cli.github.com/).

The following aliases offer some shortcuts to handle your daily tasks.

## Aliases

### GLab on Windows installations

`alias glab='winpty glab "$@"'`

This is tricky. Most commands do not run on Windows. But if you use the `winpty` command, it works. If you ever need
to execute the virgin `glab` command, do it this way: `\glab`. This skips your alias and executes the original command.

### Create a merge request

<!-- command shall be displayed as one line -->
<!-- markdownlint-disable-next-line MD013 -->
`alias cmr='create_mr(){ glab mr create --draft --fill --fill-commit-body -y --remove-source-branch -a $GITLAB_USERNAME -l "$1" -t "$2"; unset -f create_mr; }; create_mr'`

This alias creates a new draft merge request for the current branch, assigns it to your user (don't forget to set the
`GITLAB_USERNAME` variable), adds a specific title and fills the description with all commit messages of the current branch.
Labels can be added as well.

Usage: `cmr label1,label2' 'chore: my specific title here'`

### Show the current merge request

`alias smr='glab mr show --web'`

A low brainer. Opens the current merge request in your browser.

### Show all merge requests I have to review

<!-- command shall be displayed as one line -->
<!-- markdownlint-disable-next-line MD013 -->
`alias lrev='\glab api "groups/$GITLAB_ROOT_GROUP/merge_requests?state=opened&reviewer_username=$GITLAB_USERNAME&order_by=created_at&sort=desc" | jq -r "(.[] | [(.author.name | \" \"* (26-length) + .), .title]) | @tsv"'`

My favorite one. Lists all merge requests I have to review. Needs the `GITLAB_USERNAME` variable to be set and the `GITLAB_ROOT_GROUP`
set to the group ID to start the search at. The output looks like this:

```text
Technischer User      chore(deps): update dependency com.hlag.my.group:artifact to v1.13.37
Technischer User      chore(deps): update puppeteer packages to v19 (major)
      Max Muster      feat: add dlq functionality
```

### Show a merge request to review

<!-- command shall be displayed as one line -->
<!-- markdownlint-disable-next-line MD013 -->
`alias srev='show_mr(){ chrome $(\glab api "groups/$GITLAB_ROOT_GROUP/merge_requests?state=opened&reviewer_username=$GITLAB_USERNAME&order_by=created_at&sort=desc" | jq -r ".[$1-1].web_url"); unset -f show_mr; }; show_mr'`

Use the `lrev` command from above and then execute a `srev 1` to open the first merge request in your browser. The alias opens the
merge request in Chrome.
