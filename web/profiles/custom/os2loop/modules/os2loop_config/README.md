# OS2Loop Config

Adds some useful commands for managing config:

```sh
      os2loop:config:rename                         Rename config.
      os2loop:config:add-module-config-dependencies Add module dependencies in config.
      os2loop:config:move-module-config             Move config info config/install folder in a module.
```

**Note**: The `os2loop:config:move-module-config` command is somewhat is similar
to the
[`config:export:content:type`](https://drupalconsole.com/docs/en/commands/config-export-content-type)
command the [Drupal Console](https://drupalconsole.com/), but that command does
not add the dependencies needed for our requirements.
