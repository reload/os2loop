# OS2Loop Documents

Documents and document collections.

## Legacy documents

*Legacy document* are documents with a non-empty body field and these exist only
as content migrated from the old OS2Loop system. The body field is only
available on legacy documents.

## Fixtures

```sh
vendor/bin/drush --yes pm:enable os2loop_documents_fixtures
vendor/bin/drush --yes content-fixtures:load --groups=os2loop_documents,os2loop_file,os2loop_taxonomy
vendor/bin/drush --yes pm:uninstall content_fixtures
```
