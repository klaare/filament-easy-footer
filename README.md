#  Filament EasyFooter


![Filament Easy Footer cover](./art/cover.webp)


[![Latest Version on Packagist](https://img.shields.io/packagist/v/devonab/filament-easy-footer.svg?style=flat-square)](https://packagist.org/packages/devonab/filament-easy-footer)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/devonab/filament-easy-footer/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/devonab/filament-easy-footer/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/devonab/filament-easy-footer/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/devonab/filament-easy-footer/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)[![Total Downloads](https://img.shields.io/packagist/dt/devonab/filament-easy-footer.svg?style=flat-square)](https://packagist.org/packages/devonab/filament-easy-footer)



This filament Plugin provides an easy and flexible way to add a customizable footer to your FilamentPHP application. This plugin integrates seamlessly with Filament's admin interface, enabling you to enhance your application's user experience with a good looking footer.

## Navigation

---

- [Installation](#installation)
- [Usage](#usage)
- [Configurations](#configurations)
    - [Footer position](#footer-position)
    - [Show GitHub version](#show-github-version)
    - [Show load time](#load-time)
    - [Add customs links](#links)
    - [Border on top](#border-on-top)
    - [Hiding from auth pages](#hiding-from-auth-pages)
- [Testing](#testing)
- [Contributing](#contributing)
- [Security Vulnerabilities](#security-vulnerabilities)
- [Credits](#credits)
- [License](#license)


## Installation

---

First, you can start to install the package via composer:

```bash
composer require devonab/filament-easy-footer
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-easy-footer-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-easy-footer-views"
```

This is the contents of the published config file:

```php
return [
    'app_name' => env('APP_NAME', 'Filament Footer'),
    'github' => [
        'repository' => env('GITHUB_REPOSITORY', ''),
        'token' => env('GITHUB_TOKEN', ''),
        'cache_ttl' => env('GITHUB_CACHE_TTL', 3600)
    ],
];
```

## Usage

---

To start using this plugin, simply add it to the Filament provider's plugin array.

```php
use Devonab\FilamentEasyFooter\EasyFooterPlugin;

->plugins([
    EasyFooterPlugin::make(),
])
```

## Configurations

---

### Footer position

You can choose the **position of the footer** by using this configuration :

```php
use Devonab\FilamentEasyFooter\EasyFooterPlugin;

->plugins([
    EasyFooterPlugin::make()
    ->withFooterPosition('footer'),
])
```

You can choose between 3 positions, represented by their corresponding [render hooks](https://filamentphp.com/docs/3.x/support/render-hooks)

- `footer` : panels::footer (by default)
- `sidebar` : panels::sidebar.nav.end
- `sidebar.footer` : panels::sidebar.footer

```php
use Devonab\FilamentEasyFooter\EasyFooterPlugin;

->plugins([
    EasyFooterPlugin::make()
    ->withFooterPosition('footer'),
])
```
![Filament Easy Footer position](./art/position_footer.webp)

```php
use Devonab\FilamentEasyFooter\EasyFooterPlugin;

->plugins([
    EasyFooterPlugin::make()
    ->withFooterPosition('sidebar'),
])
```
![Filament Easy Footer sidebar position](./art/position_sidebar.webp)

```php
use Devonab\FilamentEasyFooter\EasyFooterPlugin;

->plugins([
    EasyFooterPlugin::make()
    ->withFooterPosition('sidebar.footer'),
])
```
![Filament Easy Footer sidebar footer position](./art/position_sidebar_footer.webp)

### Show GitHub version
![Filament Easy Footer github](./art/github_version.webp)

You can show the **GitHub version** of your application by using this configuration :
```php
use Devonab\FilamentEasyFooter\EasyFooterPlugin;

->plugins([
    EasyFooterPlugin::make()
    ->withGithub(showLogo: true, showUrl: true)
])
```
- showLogo : Display the GitHub logo next to the version
- showUrl : Add an `<a>` tag to the Github URL around the logo

To make this one work, you need to add this keys to our .env file :

```bash
GITHUB_REPOSITORY=user/name-of-the-repo
GITHUB_TOKEN=
GITHUB_CACHE_TTL= # in seconds, 3600 by default
```

You can generate a token [here](https://github.com/settings/personal-access-tokens). The token need to have at least the `read-only` permission on the "Contents" scope in Repository permissions.

### Load time
![Filament Easy Footer load time](./art/load_time.webp)

If you want to display the **page load time**, you can use this configuration :

```php
use Devonab\FilamentEasyFooter\EasyFooterPlugin;

->plugins([
    EasyFooterPlugin::make()
    ->withLoadTime(),
])
```
### Links
![Filament Easy Footer links](./art/links.webp)

You can add **custom links** (3 links max) to the footer by using this configuration :
```php
use Devonab\FilamentEasyFooter\EasyFooterPlugin;

->plugins([
    EasyFooterPlugin::make()
    ->withLinks([
        ['title' => 'About', 'url' => 'https://example.com/about'],
        ['title' => 'CGV', 'url' => 'https://example.com/cgv'],
        ['title' => 'Privacy Policy', 'url' => 'https://example.com/privacy-policy']
    ]),
])
````


### Border on top
You can add a border on the top of the footer by using this configuration : 

```php
use Devonab\FilamentEasyFooter\EasyFooterPlugin;

->plugins([
    EasyFooterPlugin::make()
    ->withBorder(),
])
```

### Hiding from auth pages
By default, the footer is also showed on the 3 auth pages : login, forgot-password and register. You can hide it by using this configuration :

```php
use Devonab\FilamentEasyFooter\EasyFooterPlugin;

->plugins([
    EasyFooterPlugin::make()
    ->hideFromAuthPages(),
])
```

---

## Testing

---

You can run the test with this command

```bash
composer test
```

## Changelog

Please see [CHANGELOG](https://github.com/Devonab/filament-easy-footer/releases) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [ Devonab](https://github.com/Devonab)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
