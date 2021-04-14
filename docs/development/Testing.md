# Testing

## UI tests

We use [Cypress](https://www.cypress.io/) to test the UI of all OS2Loop modules
and we utilize [Cypress for
Drupal](https://github.com/AmazeeLabs/cypress/blob/8.x-1.x/README.md) to make it
easy to create and run UI tests.

### Testing a module

To add Cypress tests to a module, `os2loop_page`, say, add a sub-module called
`os2loop_page_tests_cypress` under the `os2loop_page` module:

```sh
web/profiles/custom/os2loop/modules/os2loop_page
└── modules
    ├── os2loop_page_tests
    └── os2loop_page_tests_cypress
```

## Unit tests

```sh
cd web
php core/scripts/run-tests.sh --sqlite /tmp/test.sqlite os2loop_tests
```

```sh
vendor/bin/phpunit --configuration web/core
```

## Running tests with the symfony binary

To run tests with the symfony binary we need a to patch `run-tests.sh` (cf. <https://www.drupal.org/project/drupal/issues/2748883#comment-12102000>):

```sh
cd web
curl https://www.drupal.org/files/issues/use_the_php_binary-2748883-32.patch | patch --strip=1
```

Then run tests with

```sh
symfony php core/scripts/run-tests.sh --php 'symfony php' --sqlite /tmp/test.sqlite os2loop_tests
```
