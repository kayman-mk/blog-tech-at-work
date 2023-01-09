---
date: 2023-01-09
title: "Lint everything with MegaLinter"
tags: lint
---
Happy New Year, folks!

I recently added the [MegaLinter](https://github.com/oxsecurity/megalinter/) to the [terraform-aws-gitlab-runner](https://github.com/npalm/terraform-aws-gitlab-runner/)
project.

It's a collection of linters for almost everything. It scans the repository for secrets, checks the format of Markdown files,
validates you IaC and bash scripts. Just checked their homepage: They support 53 programming languages, 24 formats and 21
tooling formats. And the best thing, it's free for all uses.

Be warned, adding such a linter after project start is not funny (better plan a working day or two depending on the size of your
project). There are so many violations that needs to be fixed. Sometimes
you simply have to deactivate some linters as they produce too many false-positives. Sometimes you ignore the rules either
globally as they are not relevant for your project or inline in your code. But don't forget to add a justification remark to
remember why you ignored the rule.

Overall it's worth to use the MegaLinter. It makes sure that all files follow a certain standard, and you don't have to search
for linters for all the different file types.

## Configuration

This is the workflow configuration. I validate the changes only to be a little faster and configured some linters different than
the default.

```yaml
  - name: MegaLinter
    id: ml
    # You can override MegaLinter flavor used to have faster performances
    # More info at https://megalinter.io/flavors/
    uses: oxsecurity/megalinter@v6
    env:
      # All available variables are described in documentation
      # https://megalinter.io/configuration/
      VALIDATE_ALL_CODEBASE: false
      GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
      # ADD YOUR CUSTOM ENV VARIABLES HERE OR DEFINE THEM IN A FILE .mega-linter.yml AT THE ROOT OF YOUR REPOSITORY
      SPELL_CSPELL_FILTER_REGEX_EXCLUDE: (\.gitignore|.tflint.hcl)
      # needed to avoid multiple error messages
      TERRAFORM_TERRASCAN_ARGUMENTS: "--non-recursive"
      # format issues fail the build
      TERRAFORM_TERRAFORM_FMT_DISABLE_ERRORS: false
      # ignore: "tags not used", "access analyzer not used", "shield advanced not used"
      TERRAFORM_KICS_ARGUMENTS: "--exclude-queries e38a8e0a-b88b-4902-b3fe-b0fcb17d5c10,e592a0c5-5bdb-414c-9066-5dba7cdea370,084c6686-2a70-4710-91b1-000393e54c12"
```

I also added a `.mega-linter.yml` file to the root of the repository. It's self-explanatory.

```yaml
---
DISABLE_LINTERS:
  # Has some problems reading referenced policy files. I created a separate workflow for TfLint as it creates valuable output.
  - TERRAFORM_TFLINT
  # Nice linter to report CVEs and other cool stuff. But it reports problems with the Terraform code which can't be disabled by
  # configuration.
  - REPOSITORY_TRIVY
  # The tables created by TfDoc are not formatted according to the standard. And there is no option to deactivate the check for a
  # section of the file.
  - MARKDOWN_MARKDOWN_TABLE_FORMATTER
  # CSpell does a great job. No need for a second linter.
  - SPELL_MISSPELL
```
