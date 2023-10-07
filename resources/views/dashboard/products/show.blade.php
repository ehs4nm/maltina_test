<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Show Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between">
                        <h1>Show Product</h1>
                        <!-- Add a "Create New Product" button or link -->
                        <div>
                            <a href="{{ route('products.index') }}" class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80 font-medium rounded-lg text-sm px-4 py-1.5 text-center mr-2 mb-2">List Products</a>
                            <a href="{{ route('products.edit', $product) }}" class="focus:outline-none text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-1.5 mr-2 mb-2 dark:focus:ring-yellow-900">Edit this Product</a>
                        </div>
                    </div>
                    <div class="">
                        <table class="table-auto border-collapse border border-slate-500">
                            <thead>
                              <tr>
                                <th class="border border-slate-600 p-2">Name</th>
                                <th class="border border-slate-600 p-2">Price</th>
                                <th class="border border-slate-600 p-2">Type</th>
                                <th class="border border-slate-600 p-2">Options</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td class="border border-slate-700 p-2">{{ $product->name }}</td>  
                                <td class="border border-slate-700 p-2">{{ $product->price }}</td>
                                <td class="border border-slate-700 p-2">{{ $product->type?->name }}</td>
                                <td class="border border-slate-700 p-2">{{ str_replace(['[', ']', '"'], ' ', $product->type?->options->pluck('name')) }}</td>
                              </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
