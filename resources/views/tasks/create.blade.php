<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Task') }}
            </h2>
            <a href="{{ route('dashboard') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                {{ __('Back to Tasks') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('tasks.store') }}">
                        @csrf

                        <!-- Title -->
                        <div class="mb-6">
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input 
                                id="title" 
                                name="title" 
                                type="text" 
                                class="mt-1 block w-full" 
                                :value="old('title')" 
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
                                placeholder="Enter task description...">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('dashboard') }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-3">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Create Task') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 