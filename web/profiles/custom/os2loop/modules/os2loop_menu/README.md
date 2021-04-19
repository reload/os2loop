# OS2Loop Menu

For now we only have fixtures in this module.

## Fixtures

```sh
vendor/bin/drush --yes pm:enable os2loop_menu
vendor/bin/drush --yes content-fixtures:load --groups=os2loop_menu,os2loop_section_page
vendor/bin/drush --yes pm:uninstall content_fixtures
```
