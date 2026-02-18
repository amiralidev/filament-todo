<?php

namespace Amiralidev\Filament\Commands;

use Illuminate\Console\Command;

class TodoCommand extends Command
{
    public $signature = 'filament-todo:install';

    public $description = 'Install the Filament Todo plugin';

    public function handle(): int
    {
        $this->comment('Filament Todo plugin installed successfully.');

        return self::SUCCESS;
    }
}
