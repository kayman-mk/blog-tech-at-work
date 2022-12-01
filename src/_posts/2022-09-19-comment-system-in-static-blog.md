---
comments_id: 65
date: 2022-09-19
title: "Implementing a comment System in a static blog"
tags: blog comment-system javascript github-issues workflow
---
This blog is powered by [Jekyll](https://jekyllrb.com/). As it is a static site generator, there are no databases,
server side scripts or anything else. Just some HTML, CSS and JavaScript files. The question now is: How to implement
a comment system for a static blog?

The easiest solution would be to integrate a third party tool like Disqus or Utterances or ... But I don't want to integrate
them as I have privacy concerns. That's why you don't find any trackers or analytics on this blog and all files are hosted
without a CDN.

As I have a GitHub account and this is a Tech blog (so all users have a GitHub account too), I found a nice solution
based on the work of [Ari Stathopoulos](https://aristath.github.io/blog/static-site-comments-using-github-issues-api)
and [Aleksandr Hovhannisyan](https://www.aleksandrhovhannisyan.com/blog/jekyll-comment-system-github-issues/). Check it
out. They use GitHub issues as a comment system. So I have my code and the comments in my GitHub repository. Really nice.

Their solution worked out of the box, but I needed some hours to adapt it to my theme and to automate the issue creation
process. What I basically did was to create a workflow which opens a GitHub issue for every new blog post I commit.

Nice!

## The workflow

```yaml
      - id: get-changed-files
        uses: jitterbit/get-changed-files@v1
        with:
          format: json
```

Fetches all file modifications and makes them available vie `steps.get-changed-files.outputs.added`. This is the base
for all following steps

```yaml
      - name: Create Github issue for new posts
        if: github.ref != 'refs/heads/main'
        run: |
          echo "Changed files: ${{ steps.get-changed-files.outputs.added }}"
          new_post=$(echo '${{ steps.get-changed-files.outputs.added }}' | jq -r .[] | grep src/_posts || true)

          if [ -n "$new_post" ]; then
            echo "New post found: $new_post"

            title=$(sed -n -e 's/^.*title: //p' "$new_post" | tr -d '"')
            issue_id=$(sed -n -e 's/^.*comments_id: //p' "$new_post")

            echo "Title: $title"
            echo "Issue ID: $issue_id"

            if [ -z "$issue_id" ]; then
              issue_response=$(curl -X "POST" "https://api.github.com/repos/kayman-mk/blog-tech-at-work/issues" \
                -H "Accept: application/vnd.github+json" \
                -H "Authorization: Bearer ${{ secrets.GITHUB_TOKEN }}" \
                -H "Content-Type: text/plain; charset=utf-8" \
                  -d "{
                  \"title\": \"$title\",
                  \"body\": \"The comments added to this issue are shown in the blog automatically.\",
                  \"labels\": [
                    \"blog-comment\",
                    \"not-published\"
                  ]
                }")

              issue_id=$(echo "$issue_response" | jq -r .number)
              echo "Created GitHub issue: $issue_id"

              sed -i "2i comments_id: $issue_id" "$new_post"
            fi
          fi
```

Checks for new posts located at `src/_posts`. If a new post is found, it extracts the title and `issue_id` from the frontmatter.
If the `issued_id` is not set, a new issue is created and the post is updated with the new `issue_id`.

```yaml
      - id: commit-changes
        uses: stefanzweifel/git-auto-commit-action@v4
        if: github.ref != 'refs/heads/main'
        with:
          commit_message: "Add comments_id to new post"
          file_pattern: src/_posts
```

Simply commit everything to the current branch.

```yaml
      - name: Activate GitHub issue for comments
        if: github.ref == 'refs/heads/main'
        run: |
          echo "Changed files: ${{ steps.get-changed-files.outputs.added }}"
          new_post=$(echo '${{ steps.get-changed-files.outputs.added }}' | jq -r .[] | grep src/_posts || true)

          if [ -n "$new_post" ]; then
            echo "New post found: $new_post"

            issue_id=$(sed -n -e 's/^.*comments_id: //p' "$new_post")

            curl -X "PATCH" "https://api.github.com/repos/kayman-mk/blog-tech-at-work/issues/$issue_id" \
              -H "Accept: application/vnd.github+json" \
              -H "Authorization: Bearer ${{ secrets.GITHUB_TOKEN }}" \
              -H "Content-Type: text/plain; charset=utf-8" \
                -d "{
                  \"state\": \"closed\",
                  \"labels\": [
                  \"blog-comment\"
                ]
              }"
          fi
```

In case the workflow runs in the `main` branch, the corresponding GitHub issue is closed and the `not-published` label is
removed.

The full file is available in my [GitHub repository](https://github.com/kayman-mk/blog-tech-at-work/blob/main/.github/workflows/build-blog.yml).
