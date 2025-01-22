# Contributing to the BigCommerce WordPress plugin

Thanks for your interest in contributing to the Wordpress for BigCommerce plugin!

The following is a set of guidelines for contributing to the project. These are just guidelines, not rules. Use your best judgment, and feel free to propose changes to this document in a pull request.

By contributing to the Wordpress for BigCommerce plugin, you agree that your contributions will be licensed as listed under the README.md.

#### Table of Contents

[How Can I Contribute?](#how-can-i-contribute)
  * [Your First Code Contribution](#your-first-code-contribution)
  * [Pull Requests](#pull-requests)
  * [Documentation](#documentation)

[Styleguides](#styleguides)
  * [Git Commit Messages](#git-commit-messages)

### Your First Code Contribution

Unsure where to begin contributing? Check our [Community group](https://forum.bigcommerce.com/s/group/0F91B000000922sSAA/developers-early-access-beta), GitHub Issues, or contribute to our documentation.

### Pull Requests

* Fill in [the required template](https://github.com/bigcommerce/bigcommerce-for-wordpress/pull/new/master)
* Include screenshots and animated GIFs in your pull request whenever possible.
* End files with a newline.

### Documentation

The v1 release of the BC for Wordpress reference docs are hosted on https://bc-wordpress-reference.vercel.app/. 

To add descriptions for classes and their methods, functions, or hooks, add a docblock to the appropriate file in the [src](https://github.com/bc-andreadao/bigcommerce-for-wordpress/tree/master/src/BigCommerce) folder.

The BC for Wordpress team will manually trigger the [Generation Documentation Github Action ](https://github.com/bc-andreadao/bigcommerce-for-wordpress/actions/workflows/doc.yml)workflow to regenerate the docs. The docs will be synced to a timestamped branch in the [bc-andreadao/bc-wordpress-reference](https://github.com/bc-andreadao/bc-wordpress-reference) repository, where your changes will be merged by a BC for Wordpress team member. 

#### Testing

The Generate Documentation workflow parses docblocks using the following parsers:

* [bc-andreadao/wp-documentor](https://github.com/bc-andreadao/wp-documentor)
* [bc-andreadao/phpdocumentor-markdown](https://github.com/bc-andreadao/phpdocumentor-markdown)

To test the parsers, install docker and run the following command in your terminal:

 `./generate-docs.sh` 

This will generate a local `docs` folder with markdown files generated from the parsers. You can change the folders that the parses parse in the [generate-docs.sh](https://github.com/bc-andreadao/bigcommerce-for-wordpress/blob/master/generate-docs.sh) file. 

## Styleguides

### Git Commit Messages

* Use the present tense ("Add feature" not "Added feature")
* Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
* Limit the first line to 72 characters or less
* Reference pull requests and external links liberally