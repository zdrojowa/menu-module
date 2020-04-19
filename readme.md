# MenuModule

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

Create menu

## Installation

Via Composer

``` bash
$ composer require zdrojowa/menu-module
```

## NPM required:

``` bash
"axios": "^0.19",
"bootstrap-vue": "2.11.0"
"vue": "^2.6.10",
"vue-multiselect": "2.1.6,
"vuedraggable": "2.23.2",
```

## Usage
- Add in webpack.mix.js

``` bash
mix.module('MenuModule', 'vendor/zdrojowa/menu-module');
```

- Add module MenuModule in config/selene.php

``` bash
'modules' => [
    MenuModule::class,
],

'menu-order' => [
    'MenuModule',
],
```

- run npm

``` bash
npm install
npm run prod
```

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [author name][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/zdrojowa/menu-module.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/zdrojowa/menu-module.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/zdrojowa/menu-module/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/zdrojowa/menu-module
[link-downloads]: https://packagist.org/packages/zdrojowa/menu-module
[link-travis]: https://travis-ci.org/zdrojowa/menu-module
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/zdrojowa
[link-contributors]: ../../contributors
