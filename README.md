# voodflow/vookiebar

Cookie consent bar for Laravel and Filament 5. Independent of any page builder: own routes, own Filament navigation group, own settings.

## Install

```bash
composer require voodflow/vookiebar
```

Publish config (optional):

```bash
php artisan vendor:publish --tag=vookiebar-config
```

Register the Filament plugin on your panel:

```php
->plugins([
    \Voodflow\Vookiebar\VookiebarPlugin::make(),
])
```

Omitting `VookiebarPlugin` hides admin pages. Public consent routes still load while `vookiebar.enabled` is true.

## Documentation

- [User manual](docs/user/index.md)
- [Developer manual](docs/developer/index.md)
- [Docs index](docs/README.md)

## License

Proprietary — production use requires a paid Voodflow license. See [LICENSE](LICENSE).
