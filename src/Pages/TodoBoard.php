<?php

namespace Amiralidev\Filament\Pages;

use Amiralidev\Filament\Models\TodoItem;
use Amiralidev\Filament\Models\TodoList;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;

class TodoBoard extends Page
{
    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected string $view = 'filament-todo::filament.pages.todo-board';

    protected static ?string $title = 'Todo Board';

    public $lists;

    public ?string $search = '';

    public function mount(): void
    {
        $this->refreshBoard();
    }

    public function updatedSearch(): void
    {
        $this->refreshBoard();
    }

    public function refreshBoard(): void
    {
        $this->lists = TodoList::query()
            ->with([
                'items' => function ($query) {
                    $query->orderBy('sort_order');
                    if ($this->search) {
                        $query->where('title', 'like', "%{$this->search}%")
                            ->orWhere('description', 'like', "%{$this->search}%");
                    }
                },
            ])
            ->orderBy('sort_order')
            ->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('createList')
                ->label('New List')
                ->model(TodoList::class)
                ->form([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                ])
                ->after(function () {
                    $this->refreshBoard();
                }),
        ];
    }

    public function reorderLists(array $order): void
    {
        foreach ($order as $index => $id) {
            TodoList::where('id', $id)->update(['sort_order' => $index]);
        }
        $this->refreshBoard();
    }

    public function reorderItems(array $order): void
    {
        // $order is structured as [{id: 1, list_id: 2}, ...] or just ids if list didn't change?
        // Actually ensuring SortableJS sends correct data is key.
        // We'll assume receiving checks for list changes too.

        foreach ($order as $itemData) {
            TodoItem::where('id', $itemData['id'])->update([
                'sort_order' => $itemData['order'],
                'todo_list_id' => $itemData['list_id'],
            ]);
        }
        $this->refreshBoard();
    }

    public function editListAction(): Action
    {
        return EditAction::make('editList')
            ->model(TodoList::class)
            ->form([
                TextInput::make('title')->required(),
            ])
            ->after(fn () => $this->refreshBoard());
    }

    public function deleteListAction(): Action
    {
        return DeleteAction::make('deleteList')
            ->model(TodoList::class)
            ->after(fn () => $this->refreshBoard());
    }

    public function createItemAction(): Action
    {
        return CreateAction::make('createItem')
            ->model(TodoItem::class)
            ->form([
                TextInput::make('title')->required(),
                Textarea::make('description'),
                Select::make('priority')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                    ])
                    ->default('normal'),
                DatePicker::make('due_date'),
                // Hidden field for list_id will be set via arguments or default
                TextInput::make('todo_list_id')->hidden(),
            ])
            ->after(fn () => $this->refreshBoard());
    }

    public function editItemAction(): Action
    {
        return EditAction::make('editItem')
            ->model(TodoItem::class)
            ->form([
                TextInput::make('title')->required(),
                Textarea::make('description'),
                Select::make('priority')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                    ]),
                DatePicker::make('due_date'),
            ])
            ->after(fn () => $this->refreshBoard());
    }

    public function deleteItemAction(): Action
    {
        return DeleteAction::make('deleteItem')
            ->model(TodoItem::class)
            ->after(fn () => $this->refreshBoard());
    }
}
