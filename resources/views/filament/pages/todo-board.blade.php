<x-filament-panels::page>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

    <div class="flex flex-col h-full space-y-4">
        <!-- Toolbar -->
        <div class="flex justify-between items-center">
            <div class="w-72">
                <x-filament::input.wrapper>
                    <x-filament::input
                        type="search"
                        wire:model.live.debounce.500ms="search"
                        placeholder="Search tasks..."
                    />
                </x-filament::input.wrapper>
            </div>
            <div>
                {{ $this->createListAction }}
            </div>
        </div>

        <!-- Board -->
        <div class="flex overflow-x-auto gap-4 pb-4 h-full"
             wire:ignore
             x-data
             x-init="
                new Sortable($refs.listsContainer, {
                    group: 'lists',
                    animation: 150,
                    handle: '.list-handle',
                    ghostClass: 'bg-gray-200',
                    onEnd: function (evt) {
                        var order = Array.from($refs.listsContainer.children).map(el => el.getAttribute('data-id'));
                        @this.reorderLists(order);
                    },
                });
             ">
            <div class="flex gap-4 h-full" x-ref="listsContainer">
                @foreach($lists as $list)
                    <div class="flex-shrink-0 w-80 bg-gray-100 dark:bg-gray-900 rounded-lg flex flex-col h-full max-h-[calc(100vh-14rem)] border border-gray-200 dark:border-gray-800"
                         data-id="{{ $list->id }}">
                        
                        <!-- List Header -->
                        <div class="p-3 flex justify-between items-center border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 rounded-t-lg list-handle cursor-move">
                            <h3 class="font-bold text-sm text-gray-700 dark:text-gray-200 truncate">
                                {{ $list->title }} 
                                <span class="text-xs text-gray-500 font-normal ml-1">({{ $list->items->count() }})</span>
                            </h3>
                            <div class="flex items-center gap-1">
                                {{ ($this->editListAction)(['record' => $list->id]) }}
                                {{ ($this->deleteListAction)(['record' => $list->id]) }}
                            </div>
                        </div>

                        <!-- Items Container -->
                        <div class="flex-1 overflow-y-auto p-2 space-y-2 min-h-[50px] items-container"
                             data-list-id="{{ $list->id }}"
                             x-init="
                                new Sortable($el, {
                                    group: 'items',
                                    animation: 150,
                                    ghostClass: 'opacity-50',
                                    onEnd: function (evt) {
                                        var itemEl = evt.item;
                                        var toListId = evt.to.getAttribute('data-list-id');
                                        var order = Array.from(evt.to.children).map((el, index) => {
                                            return {
                                                id: el.getAttribute('data-id'),
                                                order: index,
                                                list_id: toListId
                                            };
                                        });
                                        @this.reorderItems(order);
                                    }
                                });
                             ">
                            @foreach($list->items as $item)
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-md shadow-sm border border-gray-200 dark:border-gray-700 cursor-grab hover:shadow-md transition-shadow group relative"
                                     data-id="{{ $item->id }}">
                                    
                                    <div class="flex justify-between items-start mb-1">
                                        <h4 class="font-medium text-sm text-gray-900 dark:text-gray-100">{{ $item->title }}</h4>
                                        <div class="opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                                            <x-filament::icon-button
                                                icon="heroicon-m-pencil"
                                                size="xs"
                                                color="gray"
                                                wire:click="mountAction('editItem', { record: {{ $item->id }} })"
                                            />
                                            {{-- Alternative way to trigger actions if they are properly mounted --}}
                                        </div>
                                    </div>

                                    @if($item->description)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 line-clamp-2">{{ $item->description }}</p>
                                    @endif

                                    <div class="flex justify-between items-center mt-2">
                                        <div class="flex gap-2">
                                            @if($item->priority)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium uppercase tracking-wider
                                                    {{ $item->priority === 'high' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 
                                                      ($item->priority === 'normal' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 
                                                       'bg-gray-100 text-gray-700 dark:bg-gray-700/50 dark:text-gray-400') }}">
                                                    {{ $item->priority }}
                                                </span>
                                            @endif
                                        </div>

                                        @if($item->due_date)
                                            <span class="text-[10px] text-gray-400 flex items-center gap-0.5">
                                                <x-filament::icon icon="heroicon-m-calendar" class="w-3 h-3"/>
                                                {{ $item->due_date->format('M d') }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Context Menu / Actions (Hidden by default, shown on hover/click) -->
                                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100">
                                         <x-filament::dropdown>
                                            <x-slot name="trigger">
                                                <x-filament::icon-button icon="heroicon-m-ellipsis-vertical" size="xs" color="gray"/>
                                            </x-slot>
                                            <x-filament::dropdown.list>
                                                <x-filament::dropdown.list.item wire:click="mountAction('editItem', { record: {{ $item->id }} })" icon="heroicon-m-pencil">
                                                    Edit
                                                </x-filament::dropdown.list.item>
                                                 <x-filament::dropdown.list.item wire:click="mountAction('deleteItem', { record: {{ $item->id }} })" icon="heroicon-m-trash" color="danger">
                                                    Delete
                                                </x-filament::dropdown.list.item>
                                            </x-filament::dropdown.list>
                                        </x-filament::dropdown>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Footer -->
                        <div class="p-2 border-t border-gray-200 dark:border-gray-800">
                            <button wire:click="mountAction('createItem', { todo_list_id: {{ $list->id }} })"
                                    class="w-full py-1.5 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-800 rounded flex items-center justify-center transition-colors">
                                <x-filament::icon icon="heroicon-m-plus" class="w-4 h-4 mr-1"/>
                                Add Task
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <x-filament-actions::modals />
    </div>
</x-filament-panels::page>