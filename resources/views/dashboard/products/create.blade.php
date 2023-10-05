<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between">
                        <h1>Create Product</h1>
                        <!-- Add a "Create New Product" button or link -->
                        <a href="{{ route('products.index') }}" class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80 font-medium rounded-lg text-sm px-4 py-1.5 text-center mr-2 mb-2">List Products</a>
                    </div>
                    <div>
                        <form action="{{ route('products.create') }}" method="post" class="flex">
                            @csrf
                            {{-- <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6"> --}}
                                <div class="px-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                                    <div class="max-w-max">
                                        <x-input-label for="password" :value="__('name')" />
                                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                                    </div>
                                </div>
                                <div class="px-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                                    <div class="max-w-max">
                                        <x-input-label for="password" :value="__('price')" />
                                        <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="old('price')" required autofocus autocomplete="price" />
                                    </div>
                                </div>
                                <div class="px-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                                    <div class="max-w-max">
                                        <x-input-label for="password" :value="__('type')" />
                                        <x-text-input id="type" class="block mt-1 w-full" type="text" name="type" :value="old('type')" required autofocus autocomplete="type" />
                                    </div>
                                </div>
                            {{-- </div> --}}
                        </form>

                        @foreach($options as $key => $option)
                            
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
