# Filament Todo Board

[![Latest Version on Packagist](https://img.shields.io/packagist/v/amiralidev/filament-todo.svg?style=flat-square)](https://packagist.org/packages/amiralidev/filament-todo)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/amiralidev/filament-todo/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/amiralidev/filament-todo/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/amiralidev/filament-todo/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/amiralidev/filament-todo/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/amiralidev/filament-todo.svg?style=flat-square)](https://packagist.org/packages/amiralidev/filament-todo)

A powerful and beautiful Kanban-style Todo board plugin for Filament. Manage your tasks and lists with ease using a drag-and-drop interface within your Filament panels.

![Filament Todo Board](https://raw.githubusercontent.com/amiralidev/filament-todo/main/art/screenshot.png)

## Features

- **Kanban Board**: Drag and drop tasks between lists.
- **Task Management**: Create, update, and delete tasks and lists.
- **Filament Integration**: Seamlessly integrates as a page or a dashboard widget.
- **Customizable**: Easy to configure and extend.

## Installation

You can install the package via composer:

```bash
composer require amiralidev/filament-todo
```

> [!IMPORTANT]
> If you have not set up a custom theme and are using Filament Panels, follow the instructions in the [Filament Docs](https://filamentphp.com/docs/3.x/styling/overview#creating-a-custom-theme) first.

After setting up a custom theme, add the plugin's views to your theme's CSS file (usually `resources/css/filament/admin/theme.css`):

```css
@source '../../../../vendor/amiralidev/filament-todo/resources/**/*.blade.php';
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-todo-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-todo-config"
```

## Usage

### Registering the Plugin

You can register the plugin in your Panel provider (e.g., `AdminPanelProvider.php`):

```php
use Amiralidev\Filament\TodoPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            TodoPlugin::make(),
        ]);
}
```

This will automatically add the **Todo Board** page to your navigation and make the **Todo Board Widget** available.

### Customizing the Widget

If you want to use the widget on your dashboard, you can add it to your panel's `widgets()` array:

```php
use Amiralidev\Filament\Widgets\TodoBoardWidget;

public function panel(Panel $panel): Panel
{
    return $panel
        ->widgets([
            TodoBoardWidget::class,
        ]);
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Amiralidev](https://github.com/amiralidev)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
