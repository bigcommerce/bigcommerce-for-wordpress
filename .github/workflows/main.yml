workflow "Deploy" {
  on = "push"
  resolves = ["WordPress Plugin Deploy"]
}

action "tag" {
  uses = "actions/bin/filter@master"
  args = "tag"
}

action "WordPress Plugin Deploy" {
  uses = "10up/actions-wordpress/dotorg-plugin-deploy@master"
  needs = ["tag"]
  secrets = [
    "SVN_USERNAME",
    "SVN_PASSWORD",
    "GITHUB_TOKEN",
  ]
  env = {
    SLUG = "bigcommerce"
  }
}
