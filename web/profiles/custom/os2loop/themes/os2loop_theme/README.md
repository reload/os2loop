# OS2Loop theme

This is the default theme for OS2Loop.

## Building assets

Assets are built using [Symfony
Encore](https://symfony.com/doc/current/frontend/encore/installation.html#installing-encore-in-non-symfony-applications).

Build assets (JavaScript and CSS) by running

```sh
docker run --volume ${PWD}:/app --workdir /app node:latest yarn install
docker run --volume ${PWD}:/app --workdir /app node:latest yarn build
```

During development you may want to run it with your locally installed
[`yarn`](https://classic.yarnpkg.com/en/docs/install/) binary:

```sh
yarn install
yarn build
```

and maybe even watch for changes:

```sh
yarn watch
```
