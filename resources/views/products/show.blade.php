@extends('layouts.app')
@section('title', $product->name . ' — BABB Portaal')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Producten</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
            {{ $product->is_active ? 'Actief' : 'Inactief' }}
        </span>
    </div>
    <a href="{{ route('products.edit', $product) }}"
       class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        Bewerken
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <dl class="grid grid-cols-2 md:grid-cols-4 gap-6 text-sm">
        <div><dt class="text-gray-500">SKU</dt><dd class="font-mono font-medium mt-1">{{ $product->sku ?? '—' }}</dd></div>
        <div><dt class="text-gray-500">Categorie</dt><dd class="font-medium mt-1">{{ $product->category ?? '—' }}</dd></div>
        <div><dt class="text-gray-500">Prijs excl. BTW</dt><dd class="font-medium mt-1">&euro; {{ number_format($product->price, 2, ',', '.') }}</dd></div>
        <div><dt class="text-gray-500">BTW {{ $product->tax_rate }}%</dt><dd class="font-medium mt-1">&euro; {{ number_format($product->price_including_tax, 2, ',', '.') }} incl.</dd></div>
    </dl>
    @if ($product->description)
    <div class="mt-4 pt-4 border-t border-gray-100 text-sm text-gray-700">{{ $product->description }}</div>
    @endif
</div>
@endsection
