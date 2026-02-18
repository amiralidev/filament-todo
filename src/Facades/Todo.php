<?php

namespace Amiralidev\Filament\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Amiralidev\Filament\Todo
 */
class Todo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Amiralidev\Filament\Todo::class;
    }
}
