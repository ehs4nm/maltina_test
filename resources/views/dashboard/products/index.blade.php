<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between">
                        <h1>Product List</h1>
                        
                        <!-- Add a "Create New Product" button or link -->
                        <a href="{{ route('products.create') }}" class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80 font-medium rounded-lg text-sm px-4 py-1.5 text-center mr-2 mb-2">Create New Product</a>
                    </div>
            
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">ID</th>
                                <th class="px-6 py-3">Name</th>
                                <th class="px-6 py-3">Slug</th>
                                <th class="px-6 py-3">Price</th>
                                <th class="px-6 py-3">Type(Options)</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <td class="px-6 py-4">{{ $product->id }}</td>
                                    <td class="px-6 py-4">{{ $product->name ?: '-' }}</td>
                                    <td class="px-6 py-4">{{ $product->slug ?: '-' }}</td>
                                    <td class="px-6 py-4">{{ $product->price ?: '-' }}</td>
                                    <td class="px-6 py-4">{{ $product->type ? $product->type->name . ': ' .  str_replace(['[', ']', '"'], ' ',$product->type?->options->pluck('name')) : '-' }}</td>
                                    <td class="flex px-6 py-4">
                                        <!-- Add buttons for editing and deleting products -->
                                        <a href="{{ route('products.edit', $product->id) }}" class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80 font-medium rounded-lg text-sm px-4 py-1.5 text-center mr-2 mb-2">Edit</a>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" type="submit" class="text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 shadow-lg shadow-red-500/50 dark:shadow-lg dark:shadow-red-800/80 font-medium rounded-lg text-sm px-4 py-1.5 text-center mr-2 mb-2">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="text-white">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
