# BigCommerce Plugin Resources

The Resources page in the plugin admin will show a collection of resources
that can be used to enhance, extend, and support the BigCommerce plugin
for specific use cases.

The page is powered by an API that returns the collection of resources
as a JSON object.

## Structure

The response should be divided into "sections", each of which will
be displayed as a tab on the resources page. Each section should have a
`label` and a list of `resources`.

Each resource requires a `name` and a `url`, and optionally a `description`,
URLs to `thumbnail` images at both low-res (300x140) and hi-res (600×280), and
categories.

```
{
    "version": 1,
    "sections": [
        {
            "label": "Themes",
            "resources": [
                {
                    "name": "A Theme",
                    "description": "",
                    "thumbnail": {
                        "small": "https:\/\/placem.at\/things?w=300&h=140&random=A%20Theme&txt=A%20Theme",
                        "large": "https:\/\/placem.at\/things?w=600&h=280&random=A%20Theme&txt=A%20Theme"
                    },
                    "url": "https:\/\/example.com\/a-theme\/",
                    "categories": [ 'Blue', 'Green' ],
                    "isExternal": true
                }
            ]
        },
        {
            "label": "Support Links",
            "resources": [
                {
                    "name": "BigCommerce Help Center",
                    "description": "Welcome! How can we help?",
                    "thumbnail": {
                        "small": "",
                        "large": ""
                    },
                    "url": "https:\/\/support.bigcommerce.com\/",
                    "categories": [],
                    "isExternal": true
                }
            ]
        }
    ]
}
```

## Building the JSON data

There is a WP-CLI command to build the JSON structure from data in a
CSV file. The CSV requires fields labeled "Tab", "Name", "Description",
"URL", "Thumbnail", "HiRes Thumbnail", and "Categories". See sample data
at https://docs.google.com/spreadsheets/d/14ChXtlATHZpG2rESQBeXmDyv_8_DjMeK6gW5ElVFT1I/edit?usp=sharing

To build the JSON, give the path to the CSV as an argument to the CLI command
and redirect the output to a JSON file.

```
wp bigcommerce resources build --pretty /path/to/your/resources.csv > /path/to/your/resources.json
```

This command only works when installing the plugin from source and running
`composer install`, as it requires the `league/csv` library, which is not
included in the official package.