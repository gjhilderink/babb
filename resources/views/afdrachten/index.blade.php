@extends('layouts.app')
@section('title', 'Afdracht Bonboys – BABB Portaal')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold text-gray-900">Afdracht Bonboys</h1>
    <a href="{{ route('afdrachten.create') }}"
       class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + Nieuwe afdracht
    </a>
</div>

{{-- Samenvatting --}}
<div class="grid grid-cols-2 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="text-xs text-gray-500 mb-1">Totaal betaald</div>
        <div class="text-xl font-bold text-green-600">&euro; {{ number_format($totaalBetaald, 2, ',', '.') }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="text-xs text-gray-500 mb-1">Nog te betalen</div>
        <div class="text-xl font-bold text-orange-500">&euro; {{ number_format($totaalOpenstaand, 2, ',', '.') }}</div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="mb-5 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Zoeken op onderwerp…"
           class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-52 focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">Alle statussen</option>
        <option value="nieuw"          @selected(request('status') === 'nieuw')>Nieuw</option>
        <option value="nog_te_betalen" @selected(request('status') === 'nog_te_betalen')>Nog te betalen</option>
        <option value="betaald"        @selected(request('status') === 'betaald')>Betaald</option>
    </select>
    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg">Filteren</button>
    @if (request('search') || request('status'))
        <a href="{{ route('afdrachten.index') }}" class="text-sm text-gray-400 hover:text-gray-600 self-center">Wissen</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if ($afdrachten->isEmpty())
        <p class="px-6 py-10 text-center text-sm text-gray-400">Geen afdrachten gevonden.</p>
    @else
    <table class="min-w-full divide-y divide-gray-100 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Onderwerp</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Bedrag</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Datum</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Status</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Aangemaakt door</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($afdrachten as $afdracht)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <div class="font-medium text-gray-900">{{ $afdracht->onderwerp }}</div>
                    @if ($afdracht->notities)
                    <div class="text-xs text-gray-400 mt-0.5">{{ Str::limit($afdracht->notities, 70) }}</div>
                    @endif
                </td>
                <td class="px-5 py-3 font-medium text-gray-800">
                    &euro; {{ number_format($afdracht->bedrag, 2, ',', '.') }}
                </td>
                <td class="px-5 py-3 text-gray-600">
                    {{ $afdracht->datum ? $afdracht->datum->format('d-m-Y') : '—' }}
                </td>
                <td class="px-5 py-3">
                    <form method="POST" action="{{ route('afdrachten.status', $afdracht) }}">
                        @csrf @method('PATCH')
                        <select name="status" onchange="this.form.submit()"
                                class="text-xs font-medium rounded-full px-2 py-0.5 border-0 cursor-pointer focus:outline-none {{ $afdracht->statusColor() }}">
                            <option value="nieuw"          @selected($afdracht->status === 'nieuw')>Nieuw</option>
                            <option value="nog_te_betalen" @selected($afdracht->status === 'nog_te_betalen')>Nog te betalen</option>
                            <option value="betaald"        @selected($afdracht->status === 'betaald')>Betaald</option>
                        </select>
                    </form>
                </td>
                <td class="px-5 py-3 text-gray-600">{{ $afdracht->creator->name }}</td>
                <td class="px-5 py-3 text-right flex justify-end gap-3">
                    <a href="{{ route('afdrachten.edit', $afdracht) }}" class="text-xs text-bb-green-600 hover:underline font-medium">Bewerken</a>
                    <form method="POST" action="{{ route('afdrachten.destroy', $afdracht) }}"
                          onsubmit="return confirm('Afdracht verwijderen?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-400 hover:text-red-600">Verwijderen</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if ($afdrachten->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $afdrachten->links() }}</div>
    @endif
    @endif
</div>
@endsection
