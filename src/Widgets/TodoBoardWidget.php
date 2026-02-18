<?php

namespace Amiralidev\Filament\Widgets;

use Amiralidev\Filament\Pages\TodoBoard;
use Filament\Widgets\Widget;

class TodoBoardWidget extends Widget
{
    protected string $view = 'filament-todo::filament.widgets.todo-board-widget';

    protected int|string|array $columnSpan = 'full';

    public function mount(): void
    {
        // Logic to fetch data, similar to TodoBoard
    }
}
