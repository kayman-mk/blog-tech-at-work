---
date: 2022-07-25
---
# Be faster on the command line: Git

## Motivation

I am using the command line day by day for my Git tasks: cloning repositories, creating new branches, committing changes.
One paint point always was the time it took me to type in the commands, especially for Git. So I sat down and thought
about some useful aliases which could make my life easier.

Put these aliases into your `~/.bashrc` and enjoy.

## Aliases

### Make the Git directory the current directory

`alias gcd='cd /use/your/top/level/git/directory/here'`

Just hardcode the path to your top level Git directory.

### Show the current status

`alias gs='git status'`

A simple one and pretty straightforward. Tells you everything about your working copy.

### Jump to the root directory within the current Git repository

`alias gtop='cd $(git rev-parse --show-toplevel)'`

Wherever you are in your Git repository, after executing `gtop` you will be in the root directory.

### Create a new branch

`alias gctb='create_task_branch(){ git checkout $(gdb); git pull; git checkout -b "$USERNAME/$1"; unset -f create_task_branch; }; create_task_branch'`

`gctb ticket-123/first-task` creates a new branch with the name `<username>/ticket-123/first-task` based on the default branch
of the repository. Before creating the branch, the latest changes from the default branch are pulled.

### Create a commit

`alias gac="git add . && echo $(gcb | cut -d '/' -f2 | tr \[a-z\] \[A-Z\]) | git commit -e -F -"`

Adds all your changes to the staging area and creates a commit on the current branch. Your favorite editor is opened and
the commit message is prefilled with the branch name in uppercase letters (your username prefix is removed). Either enter
a message or use the default message. Simply close the window and the commit is created.

Consider to set the `core.editor` setting to use a different editor.

### Create a commit and push it

`alias gacp="gac && (git push -u || git push --set-upstream origin $(gcb))"`

Same as before but now your branch is pushed to the remote repository. `origin` has to point to a repository which is
writable for you. In case no upstream is set, the alias does it.

### Get the default branch

`alias gdb="git remote show origin | sed -n '/HEAD branch/s/.*: //p'"`

Calculates the default branch of the current repository. Usually it is `master` or `main` but sometimes it is totally different. Used
by various aliases above.

### Get the current branch

`alias gcb='git branch | grep \* | cut -d " " -f2'`

Show the name of the current branch.
