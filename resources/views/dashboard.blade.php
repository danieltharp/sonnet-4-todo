<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Tasks') }}
            </h2>
            <a href="{{ route('tasks.create') }}" 
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ __('New Task') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @php
                $user = Auth::user();
                $incompleteTasks = $user->tasks()->where('status', 'incomplete')->orderBy('created_at', 'asc')->get();
                $completeTasks = $user->tasks()->where('status', 'complete')->orderBy('completed_at', 'desc')->get();
                $totalTasks = $incompleteTasks->count() + $completeTasks->count();
            @endphp

            @if($totalTasks === 0)
                <!-- Empty State -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="text-gray-500 text-lg mb-4">
                            {{ __("You don't have any tasks yet!") }}
                        </div>
                        <div class="text-gray-400 mb-6">
                            {{ __("Create your first task to get started with your todo list.") }}
                        </div>
                        <a href="{{ route('tasks.create') }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                            {{ __('Create Your First Task') }}
                        </a>
                    </div>
                </div>
            @else
                <!-- Task Statistics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold text-gray-800">{{ $totalTasks }}</div>
                                <div class="text-sm text-gray-600">Total Tasks</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-yellow-600">{{ $incompleteTasks->count() }}</div>
                                <div class="text-sm text-gray-600">Incomplete</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-green-600">{{ $completeTasks->count() }}</div>
                                <div class="text-sm text-gray-600">Complete</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Incomplete Tasks -->
                @if($incompleteTasks->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                {{ __('Incomplete Tasks') }} ({{ $incompleteTasks->count() }})
                            </h3>
                            <div class="space-y-3">
                                @foreach($incompleteTasks as $task)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-medium text-gray-800">{{ $task->title }}</h4>
                                                @if($task->description)
                                                    <p class="text-gray-600 text-sm mt-1">{{ Str::limit($task->description, 100) }}</p>
                                                @endif
                                                <p class="text-xs text-gray-400 mt-2">Created {{ $task->created_at->format('M j, Y') }}</p>
                                            </div>
                                            <div class="flex items-center space-x-2 ml-4">
                                                <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-green-600 hover:text-green-800" title="Mark as Complete">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                                <a href="{{ route('tasks.edit', $task) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this task?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Complete Tasks -->
                @if($completeTasks->count() > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                {{ __('Complete Tasks') }} ({{ $completeTasks->count() }})
                            </h3>
                            <div class="space-y-3">
                                @foreach($completeTasks as $task)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 opacity-75">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-medium text-gray-600 line-through">{{ $task->title }}</h4>
                                                @if($task->description)
                                                    <p class="text-gray-500 text-sm mt-1 line-through">{{ Str::limit($task->description, 100) }}</p>
                                                @endif
                                                <p class="text-xs text-gray-400 mt-2">
                                                    Completed {{ $task->completed_at->format('M j, Y') }}
                                                </p>
                                            </div>
                                            <div class="flex items-center space-x-2 ml-4">
                                                <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-yellow-600 hover:text-yellow-800" title="Mark as Incomplete">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                                <a href="{{ route('tasks.edit', $task) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this task?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
