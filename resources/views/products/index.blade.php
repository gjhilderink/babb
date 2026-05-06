@extends('layouts.app')
@section('title', 'Producten â€” BABB Portaal')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Producten</h1>
    <a href="{{ route('products.create') }}"
       class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + Nieuw product
    </a>
</div>

<form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Zoek op naam of SKU..."
           class="flex-1 min-w-48 border border-gray-300 rounded-lg px-3 py-2 text-sm">
    @if ($categories->isNotEmpty())
    <select name="category" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">Alle categorieÃ«n</option>
        @foreach ($categories as $cat)
            <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
        @endforeach
    </select>
    @endif
    <button type="submit" class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm px-4 py-2 rounded-lg">Zoeken</button>
    <a href="{{ route('products.index') }}" class="text-sm text-gray-500 px-3 py-2 hover:text-gray-800">Wis filters</a>
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Naam</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">SKU</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Categorie</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-600">Prijs (excl.)</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-600">BTW %</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($products as $product)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-900">
                    <a href="{{ route('products.show', $product) }}" class="text-bb-green-700 hover:underline">{{ $product->name }}</a>
                </td>
                <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $product->sku ?? 'â€”' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $product->category ?? 'â€”' }}</td>
                <td class="px-4 py-3 text-right font-medium">&euro; {{ number_format($product->price, 2, ',', '.') }}</td>
                <td class="px-4 py-3 text-right text-gray-600">{{ $product->tax_rate }}%</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $product->is_active ? 'Actief' : 'Inactief' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('products.edit', $product) }}" class="text-xs text-bb-green-600 hover:underline">Bewerken</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">Geen producten gevonden.</td>
            </tr>
            @endforelse
        </tbody>
    </table></div><div class="px-4 py-3 border-t border-gray-100">{{ $products->links() }}</div>
</div>
@endsection



