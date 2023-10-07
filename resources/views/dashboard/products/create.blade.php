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
                    <div x-data="{ showTypeId: false, selectedTypeName: '', typeName:'' }">
                        <form action="{{ route('products.store') }}" method="post" class="flex">
                            @csrf
                            <div>
                                @if($errors->any())
                                    <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 mb-2" role="alert">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li class="font-semibold mr-2 text-left flex-auto">{{ $error }}</li> 
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="px-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                                    <div class="max-w-max">
                                        <label for="name"></label>
                                        <input type="text" name="name" id="name" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required autofocus autocomplete="name">
                                    </div>
                                </div>
                                <div class="px-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                                    <div class="max-w-max">
                                        <label for="price"></label>
                                        <input type="number" name="price" id="price" min=0 class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required autofocus autocomplete="price">
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div>
                                    <div class="px-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                                        <div class="max-w-max">
                                            <label for="type">Create a new Type <small>or select from drop down below</small></label>
                                            <input x-model="typeName" type="text" name="type" id="type" min=0 class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" value="{{ old('type') }}" required autofocus autocomplete="type" placeholder="Name of a new Type to be created">
                                        </div>
                                    </div>
                                    <div class="px-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                                        <label for="countries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select a type for the product</label>
                                        <select x-model:click="selectedTypeName" x-on:change="showTypeId = true; typeName = selectedTypeName" name="type_id" id="type_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            @foreach($types as $key => $type)
                                                <option value="{{ $type->name }}">{{ $type->name }}: {{ str_replace(['[', ']', '"'], ' ', $type->options?->pluck('name')) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div x-show="showTypeId" class="my-16">
                                    @foreach($types as $key => $type)
                                        <div x-show="selectedTypeName == '{{ $type->name }}'">
                                            <strong class="text-green-600">{{ $type->name }}: </strong>
                                            {{ str_replace(['[', ']', '"'], ' ', $type->options?->pluck('name')) }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80 font-medium rounded-lg text-sm px-4 py-1.5 text-center mr-2 mb-2">{{ __("Create")}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
