![Status](https://github.com/kayman-mk/blog-tech-at-work/actions/workflows/build-blog.yml/badge.svg)

# Tech@Work

This repository contains the content of my blog Tech@Work.

## Workflows

- Linting:
  - YAML
  - PHP
  - Markdown files
  - Dockerfiles
  - the title of pull requests
  - Github workflow files
- Semantic release

## Comment System

As this is a static site, I need a place for the comments for each article. I decided to put them into GitHub issues.
This integrates nicely into my daily workflow and it's fully automated:

- when a new post is created, the pipeline creates a new issue with the title of the post and the labels `blog-comment`
  and `not-published`.
- the issue id is added to the post's frontmatter (with a commit from the workflow)
- as soon as the PR is merged into the `main` branch, the issue is closed, the label `not-published` is removed

But the main work was done by others: [Ari Stathopoulos](https://aristath.github.io/blog/static-site-comments-using-github-issues-api)
created the basis for the comment system in 2019. And [Aleksandr Hovhannisyan](https://www.aleksandrhovhannisyan.com/blog/jekyll-comment-system-github-issues/)
extended this system in 2020 and 2021 with really useful JavaScript.

I integrated this comment system into my blog and changed the layout a little to integrate better with the Minimal Mistake
theme I use.

## Run Jekyll locally

Execute the `start_blog.sh` script. It builds a Docker image with all prerequisites and serves the blog
on your local machine at `http://localhost:4000/`.
