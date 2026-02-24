#!/bin/bash

# To run this file, run the command `./generate-docs.sh` in the terminal

# Clean up existing docs directory completely
rm -rf docs
mkdir -p docs
echo "Created clean directory for markdown template inside docs."

# Generate hook documentation in Markdown format using Pronamic WP Documentor
vendor/bin/wp-documentor parse src --format=markdown --output=docs/Hooks.md

# Generate documentation for classes in Markdown format using PHPDocumentor
docker run --rm \
  -v "$PWD:/data" \
  -v "$PWD/vendor/bc-andreadao/phpdocumentor-markdown:/phpdoc" \
  phpdoc/phpdoc:3 \
  -d /data/src \
  -t /data/docs \
  --template=/phpdoc/themes/markdown \
  --visibility=public,protected