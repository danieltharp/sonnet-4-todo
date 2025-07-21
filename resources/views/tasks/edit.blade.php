<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Task') }}
            </h2>
            <a href="{{ route('dashboard') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                {{ __('Back to Tasks') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <!-- Status Toggle Form (moved outside main form) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Current Status:</p>
                                <p class="text-sm text-gray-800">
                                    @if($task->isComplete())
                                        <span class="text-green-600">✓ Complete</span> - Completed {{ $task->completed_at->format('M j, Y \a\t g:i A') }}
                                    @else
                                        <span class="text-yellow-600">○ Incomplete</span> - Created {{ $task->created_at->format('M j, Y \a\t g:i A') }}
                                    @endif
                                </p>
                            </div>
                            <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                @if($task->isComplete())
                                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        Mark as Incomplete
                                    </button>
                                @else
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        Mark as Complete
                                    </button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Update Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('tasks.update', $task) }}">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div class="mb-6">
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input 
                                id="title" 
                                name="title" 
                                type="text" 
                                class="mt-1 block w-full" 
                                :value="old('title', $task->title)" 
                                required 
                                autofocus 
                                autocomplete="title" 
                                placeholder="Enter task title..." />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <x-input-label for="description" :value="__('Description (Optional)')" />
                            <textarea 
                                id="description" 
                                name="description" 
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                rows="4" 
                                placeholder="Enter task description...">{{ old('description', $task->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('dashboard') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-3">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Task') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 