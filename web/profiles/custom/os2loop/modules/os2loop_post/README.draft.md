# OS2Loop Post

## Fixtures

```sh
vendor/bin/drush --yes pm:enable os2loop_post_fixtures
vendor/bin/drush --yes content-fixtures:load \
  --groups=os2loop_post,os2loop_file,os2loop_taxonomy
vendor/bin/drush --yes pm:uninstall content_fixtures
```

## Automated UI tests

```sh
TEST_SESSION_ENABLED=true vendor/bin/drush serve &
vendor/bin/drush --yes pm:enable os2loop_post_tests_cypress
CYPRESS_DRUPAL_DRUSH=$PWD/vendor/bin/drush $PWD/vendor/bin/drush cypress:run os2loop_post_tests_cypress
```

Use `…/drush cypress:open` to run tests interactively, i.e.

```sh
CYPRESS_DRUPAL_DRUSH=$PWD/vendor/bin/drush $PWD/vendor/bin/drush cypress:open
```

Screenshots of any failing test runs can be found in
`web/drupal-cypress-environment/cypress/screenshots`.

## Quick tests

```sh
# Reset your OS2Loop installation to a known state
vendor/bin/drush --yes site:install os2loop --existing-config
vendor/bin/drush --yes pm:enable os2loop_post_fixtures
vendor/bin/drush --yes content-fixtures:load
# Start your web server
TEST_SESSION_ENABLED=true symfony local:server:start
# Run some tests
yarn install
CYPRESS_BASE_URL=https://127.0.0.1:8000 yarn cypress open --project web/profiles/custom/os2loop/modules/os2loop_post/modules/os2loop_post_tests_cypress/tests/

#CYPRESS_BASE_URL=https://127.0.0.1:8000 web/drupal-cypress-environment/node_modules/.bin/cypress open --project web/profiles/custom/os2loop/modules/os2loop_post/modules/os2loop_post_tests_cypress/tests/
```
