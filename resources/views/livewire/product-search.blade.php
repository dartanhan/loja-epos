<x-app-layout xmlns:wire="http://www.w3.org/1999/xhtml">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
        <div>
            <input wire:model.debounce.300ms="query" type="text" placeholder="Digite o nome do produto">
            <ul>
{{--                @foreach($products as $product)--}}
{{--                    <li wire:click="selectProduct('{{ $product->name }}')">{{ $product->name }}</li>--}}
{{--                @endforeach--}}
            </ul>
        </div>
</x-app-layout>
