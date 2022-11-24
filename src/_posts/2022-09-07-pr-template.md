---
comments_id: 48
date: 2022-09-07
title: "Templates for Pull Requests"
---
Have you ever seen Pull Requests like "fixing yesterdays bug" or similar? That's horrible especially if you think
about your commit history.

Unfortunately you can't validate the description of a Pull Request automatically (the title can be validated to some extent.
See [Linting Pull Requests](lint-pr.html) for more details). But you can give your users at least some guidance what you
expect from them. This is especially useful for open source development on GitHub but also a good idea for your internal
teams to boost the quality of your Pull Requests.

## Typical Pull Request Template

Place the following template in `.github/pull_request_template.md`. It will be used automatically for all Pull Requests created.

```markdown
# Description

What is the overall goal of your PR? Which problem does it solve? Please also include relevant motivation and context.
List any dependencies that are required for this change.

Fixes #(issue number)

# Migrations required

- yes: please describe the migration
- no: please delete the whole paragraph

# Checklist

- [ ] My code follows the style guidelines of the project
- [ ] I have performed a self-review of my own code
- [ ] I have made corresponding changes to the documentation
```

It contains of three sections. The first one states what the Pull Request is about, why it was created, what problem is
tackled and what the motivation is. It's also a good place to mention the issues which are solved or to reference any
other relevant issues.

In case it is a breaking change you have to explain in detail what your users have to do to migrate to the new version.
Of course, this paragraph should be removed if it is not a breaking change.

Lastly support the contributors with a short checklist of points you expect them to fulfill.

## More Examples

- 7 examples from [Arthur Coudouy](https://axolo.co/blog/p/part-3-github-pull-request-template)
- A wide collection of templates from [Stevemao](https://github.com/stevemao/github-issue-templates)
