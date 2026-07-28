# voodflow/voodbuilder-cookiebar

FilamentPHP 5 plugin that extends [VoodBuilder](https://github.com/voodflow/voodbuilder) with Cookie Bar.

## Install

```bash
composer require voodflow/voodbuilder-cookiebar
```

Register the Filament plugin next to VoodBuilder:

```php
->plugins([
    \Voodflow\Voodbuilder\VoodbuilderPlugin::make(),
    \Voodflow\VoodbuilderCookiebar\VoodbuilderCookiebarPlugin::make(),
])
```

Omitting `VoodbuilderCookiebarPlugin` from the panel disables Cookie Bar admin and runtime features, even while the Composer package remains installed.

Optional config (`config/voodbuilder-cookiebar.php`):

- `enabled` — master switch (default `true`)
- `auto_register_module` — register the module without the Filament plugin (default `false`, for Testbench/headless)

Scaffolded from [filamentphp/plugin-skeleton](https://github.com/filamentphp/plugin-skeleton) `5.x`.
