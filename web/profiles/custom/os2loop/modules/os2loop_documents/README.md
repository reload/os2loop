# OS2Loop Documents

Documents and document collections.

## Legacy documents

*Legacy document* are documents with a non-empty body field and these exist only
as content migrated from the old OS2Loop system. The body field is only
available on legacy documents.

## Printing to pdf

We use [Entity Print](https://www.drupal.org/project/entity_print) for
printing documents and collections, i.e converting them to PDF.

Entity Print is configured to use
[`phpwkhtmltopdf`](https://github.com/mikehaertl/phpwkhtmltopdf) for converting
HTML to PDF, and in order to make this work you need a working installation of
wkhtmltopdf 0.12.6 (see
<https://github.com/mikehaertl/phpwkhtmltopdf#installation-of-wkhtmltopdf>)
available as `/usr/local/bin/wkhtmltopdf`.

If need be, you can override the path to the `wkhtmltopdf` binary in
`settings.local.php`, e.g.:

```php
$config['entity_print.print_engine.phpwkhtmltopdf']['settings']['binary_location'] = '/opt/wkhtmltopdf/wkhtmltopdf';
```

### Debugging entity print input

See <https://www.drupal.org/node/2706755#debugging> for help on debugging the
templates and resulting HTML that will be used to generate the final PDF.

## Fixtures

```sh
vendor/bin/drush --yes pm:enable os2loop_documents_fixtures
vendor/bin/drush --yes content-fixtures:load --groups=os2loop_documents,os2loop_file,os2loop_taxonomy
vendor/bin/drush --yes pm:uninstall content_fixtures
```
