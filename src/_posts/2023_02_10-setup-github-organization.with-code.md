---
title: "Manage your GitHub organization with Terraform"
date: 2023-02-10
tags: Terraform GitHub code
---
Tired of being the administrator of your GitHub organization and setting up new repositories, teams, ... the whole
day? Having users who are creative in setting up their repositories? Checkout the
[integrations/github](https://registry.terraform.io/providers/integrations/github/latest/docs/resources/membership) Terraform
provider.

# Setup GitHub Organization In Terraform

Put the following code into your Terraform project and tell your users to create PRs to create new repositories, add
users to the organization, ... There are many resources available to setup everything in GitHub.

You can relax and approve the PRs. Don't forget to remove the admin privileges from your users and you can also consider
to remove admin privileges from the repositories.

```hcl
terraform {
  required_providers {
    github = {
      source  = "integrations/github"
      version = "5.17.0"
    }
  }

  required_version = ">= 1.0"
}

provider "github" {
  token = var.github_admin_token
  owner = "Your-Org-Name-Here"
}

variable "github_admin_token" {
  type        = string
  description = "GitHub token with admin privileges."
}

# the organization has to be created manually; used by the provider!
data "github_organization" "this" {
  name = "Your-Org-Name-Here"
}

resource "github_organization_settings" "this" {
  name = data.github_organization.name

  
  billing_email = "xxx@my-org.country"
}

resource "github_repository" "my_first" {
  name        = "nice-name"
  description = "some description"
  topics      = ["some", "topics"]
}

resource "github_membership" "user_1" {
  username = "github-user-1"
}

resource "github_membership" "user_2" {
  username = "github-user-2"
}

resource "github_team" "some_team" {
  name        = "some-team"
  description = "Some cool team"

  privacy     = "closed"
}

resource "github_team_membership" "some_team_membership" {
  team_id  = github_team.some_team.id

  username = "SomeUser"
  role     = "member"
}
```