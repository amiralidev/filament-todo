<?php

namespace Amiralidev\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Amiralidev\Filament\Pages;
use Amiralidev\Filament\Widgets;

class TodoPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-todo';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                Pages\TodoBoard::class,
            ])
            ->widgets([
                Widgets\TodoBoardWidget::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
