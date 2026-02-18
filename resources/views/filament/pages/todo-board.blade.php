<x-filament-panels::page>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

    <div class="flex flex-col h-full space-y-6">
        <!-- Toolbar -->
        <div class="flex justify-between items-center px-1">
            <div class="w-full max-w-xs">
                <x-filament::input.wrapper>
                    <x-filament::input
                        type="search"
                        wire:model.live.debounce.500ms="search"
                        placeholder="Search tasks..."
                        class="bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700/50 shadow-sm"
                    />
                </x-filament::input.wrapper>
            </div>
            <div>
                {{ $this->createListAction }}
            </div>
        </div>

        <!-- Board -->
        <div class="flex-1 overflow-x-auto pb-4"
             wire:ignore
             x-data
             x-init="
                new Sortable($refs.listsContainer, {
                    group: 'lists',
                    animation: 150,
                    handle: '.list-handle',
                    ghostClass: 'opacity-50',
                    dragClass: 'opacity-100',
                    onEnd: function (evt) {
                        var order = Array.from($refs.listsContainer.children).map(el => el.getAttribute('data-id'));
                        @this.reorderLists(order);
                    },
                });
             ">
            <div class="flex gap-6 h-full min-w-max px-1" x-ref="listsContainer">
                @foreach($lists as $list)
                    <div class="flex flex-col w-80 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-200/60 dark:border-gray-800/60 max-h-[calc(100vh-16rem)] flex-shrink-0"
                         data-id="{{ $list->id }}">
                        
                        <!-- List Header -->
                        <div class="px-4 py-3 flex justify-between items-center list-handle cursor-move group">
                            <div class="flex items-center gap-2 overflow-hidden">
                                <h3 class="font-bold text-gray-700 dark:text-gray-200 truncate tracking-tight">
                                    {{ $list->title }}
                                </h3>
                                <div class="px-2 py-0.5 rounded-full bg-gray-200/60 dark:bg-gray-800 text-xs font-medium text-gray-500 dark:text-gray-400">
                                    {{ $list->items->count() }}
                                </div>
                            </div>
                            
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                <x-filament::dropdown placement="bottom-end">
                                    <x-slot name="trigger">
                                        <button class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors rounded-md hover:bg-gray-200/50 dark:hover:bg-gray-800">
                                            <x-filament::icon icon="heroicon-m-ellipsis-horizontal" class="w-5 h-5"/>
                                        </button>
                                    </x-slot>
                                    <x-filament::dropdown.list>
                                        {{ ($this->editListAction)(['record' => $list->id]) }}
                                        {{ ($this->deleteListAction)(['record' => $list->id]) }}
                                    </x-filament::dropdown.list>
                                </x-filament::dropdown>
                            </div>
                        </div>

                        <!-- Items Container -->
                        <div class="flex-1 overflow-y-auto px-3 pb-3 space-y-3 min-h-[100px]" 
                             data-list-id="{{ $list->id }}"
                             x-init="
                                new Sortable($el, {
                                    group: 'items',
                                    animation: 200,
                                    ghostClass: 'opacity-40',
                                    dragClass: 'cursor-grabbing',
                                    delay: 10, 
                                    delayOnTouchOnly: true,
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
                                <div class="group bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700/50 hover:shadow-md dark:hover:shadow-gray-900/30 hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-200 cursor-grab active:cursor-grabbing relative"
                                     data-id="{{ $item->id }}">
                                    
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-semibold text-sm text-gray-800 dark:text-gray-100 leading-snug pr-6">
                                            {{ $item->title }}
                                        </h4>
                                        
                                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <x-filament::dropdown placement="bottom-end">
                                                <x-slot name="trigger">
                                                    <button class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                                                        <x-filament::icon icon="heroicon-m-ellipsis-vertical" class="w-4 h-4"/>
                                                    </button>
                                                </x-slot>
                                                <x-filament::dropdown.list>
                                                    <x-filament::dropdown.list.item 
                                                        wire:click="mountAction('editItem', { record: {{ $item->id }} })" 
                                                        icon="heroicon-m-pencil-square">
                                                        Edit
                                                    </x-filament::dropdown.list.item>
                                                    <x-filament::dropdown.list.item 
                                                        wire:click="mountAction('deleteItem', { record: {{ $item->id }} })" 
                                                        icon="heroicon-m-trash" 
                                                        color="danger">
                                                        Delete
                                                    </x-filament::dropdown.list.item>
                                                </x-filament::dropdown.list>
                                            </x-filament::dropdown>
                                        </div>
                                    </div>

                                    @if($item->description)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 line-clamp-2 leading-relaxed">
                                            {{ $item->description }}
                                        </p>
                                    @endif

                                    <div class="flex items-center justify-between pt-1">
                                        <div class="flex items-center gap-2">
                                            @if($item->priority && $item->priority !== 'normal')
                                                <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium border
                                                    {{ $item->priority === 'high' 
                                                        ? 'bg-red-50 text-red-600 border-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-900/30' 
                                                        : 'bg-blue-50 text-blue-600 border-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-900/30' }}">
                                                    <div class="w-1.5 h-1.5 rounded-full {{ $item->priority === 'high' ? 'bg-red-500' : 'bg-blue-500' }}"></div>
                                                    <span class="capitalize">{{ $item->priority }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        @if($item->due_date)
                                            <div class="flex items-center gap-1 text-[10px] {{ $item->due_date->isPast() ? 'text-red-500 font-medium' : 'text-gray-400' }}">
                                                <x-filament::icon icon="heroicon-m-calendar" class="w-3.5 h-3.5"/>
                                                <span>{{ $item->due_date->format('M d') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Footer / Add Button -->
                        <div class="p-3 mt-auto">
                            <button wire:click="mountAction('createItem', { todo_list_id: {{ $list->id }} })"
                                    class="w-full py-2 flex items-center justify-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white bg-transparent hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors border border-dashed border-gray-300 dark:border-gray-700 hover:border-solid hover:border-gray-400 dark:hover:border-gray-600">
                                <x-filament::icon icon="heroicon-m-plus" class="w-4 h-4"/>
                                Add Task
                            </button>
                        </div>
                    </div>
                @endforeach
                
                <!-- New List Placeholder / Button -->
                 <div class="w-80 flex-shrink-0">
                    <button wire:click="mountAction('createList')" class="w-full h-12 flex items-center justify-center gap-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 bg-gray-50/50 dark:bg-gray-900/20 border-2 border-dashed border-gray-200 dark:border-gray-800 hover:border-gray-300 dark:hover:border-gray-700 rounded-xl transition-all">
                        <x-filament::icon icon="heroicon-m-plus" class="w-5 h-5"/>
                        <span class="font-medium">Add New List</span>
                    </button>
                </div>
            </div>
        </div>
        
        <x-filament-actions::modals />
    </div>
</x-filament-panels::page>