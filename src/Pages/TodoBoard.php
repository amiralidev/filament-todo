<?php

namespace Amiralidev\Filament\Pages;

use Amiralidev\Filament\Models\TodoItem;
use Amiralidev\Filament\Models\TodoList;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;

class TodoBoard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

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
            // Actions added here will be rendered in the default header.
            // Since we are rendering the create button manually in the view, we might not need it here,
            // or we can keep it as a duplicate/fallback.
            // For now, I'll remove it from here to avoid duplication if the view handles it.
            $this->createListAction(),
        ];
    }

    public function createListAction(): Action
    {
        return CreateAction::make('createList')
            ->label('New List')
            ->model(TodoList::class)
            ->form([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ])
            ->after(function () {
                $this->refreshBoard();
            });
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
        return Action::make('editList')
            ->model(TodoList::class)
            ->label('Edit')
            ->icon('heroicon-m-pencil')
            ->color('gray')
            ->form([
                TextInput::make('title')->required(),
            ])
            ->fillForm(fn(TodoList $record): array => [
                'title' => $record->title,
            ])
            ->action(function (array $data, TodoList $record): void {
                $record->update($data);
                $this->refreshBoard();
            });
    }

    public function deleteListAction(): Action
    {
        return Action::make('deleteList')
            ->model(TodoList::class)
            ->label('Delete')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (TodoList $record): void {
                $record->delete();
                $this->refreshBoard();
            });
    }

    public function createItemAction(): Action
    {
        return Action::make('createItem')
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
                TextInput::make('todo_list_id')->hidden(),
            ])
            ->action(function (array $data, array $arguments): void {
                $listId = $arguments['todo_list_id'] ?? $data['todo_list_id'] ?? null;

                if ($listId) {
                    TodoItem::create([
                        ...$data,
                        'todo_list_id' => $listId,
                    ]);
                    $this->refreshBoard();
                }
            });
    }

    public function editItemAction(): Action
    {
        return Action::make('editItem')
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
            ->fillForm(fn(TodoItem $record): array => [
                'title' => $record->title,
                'description' => $record->description,
                'priority' => $record->priority,
                'due_date' => $record->due_date,
            ])
            ->action(function (array $data, TodoItem $record): void {
                $record->update($data);
                $this->refreshBoard();
            });
    }

    public function deleteItemAction(): Action
    {
        return Action::make('deleteItem')
            ->model(TodoItem::class)
            ->requiresConfirmation()
            ->action(function (TodoItem $record): void {
                $record->delete();
                $this->refreshBoard();
            });
    }
}
