@extends('layouts.app')
@use('App\Support\Seizoen')
@section('title', 'Overige kosten & Declaraties – BABB Portaal')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold text-gray-900">Overige kosten & Declaraties</h1>
    <a href="{{ route('declaraties.create') }}"
       class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + Nieuwe declaratie
    </a>
</div>

{{-- Seizoen selector (1 sep t/m 31 aug) --}}
<div class="mb-5 flex flex-wrap items-center gap-3">
    @foreach ($beschikbareJaren as $j)
        <a href="{{ route('declaraties.index', array_merge(request()->except('jaar','page'), ['jaar' => $j])) }}"
           title="{{ Seizoen::begin($j)->format('d-m-Y') }} t/m {{ Seizoen::eind($j)->format('d-m-Y') }}"
           class="px-4 py-1.5 rounded-full text-sm font-medium border transition
                  {{ $j == $jaar ? 'bg-bb-green-600 text-white border-bb-green-600' : 'bg-white text-gray-600 border-gray-300 hover:border-bb-green-400' }}">
            {{ Seizoen::weergave($j) }}
        </a>
    @endforeach
    @if (!$beschikbareJaren->contains(Seizoen::huidig()))
        <a href="{{ route('declaraties.index', ['jaar' => Seizoen::huidig()]) }}"
           class="px-4 py-1.5 rounded-full text-sm font-medium border bg-white text-gray-400 border-gray-200">
            {{ Seizoen::weergave(Seizoen::huidig()) }}
        </a>
    @endif
</div>

{{-- KPI totalen --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="text-xs text-gray-500 mb-1">Totaal seizoen {{ Seizoen::label($jaar) }}</div>
        <div class="text-xl font-bold text-gray-800">&euro; {{ number_format($totaalJaar, 2, ',', '.') }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="text-xs text-gray-500 mb-1">Betaald</div>
        <div class="text-xl font-bold text-green-600">&euro; {{ number_format($totaalBetaald, 2, ',', '.') }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="text-xs text-gray-500 mb-1">Nog open</div>
        <div class="text-xl font-bold text-orange-500">&euro; {{ number_format($totaalOpen, 2, ',', '.') }}</div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="mb-5 flex flex-wrap gap-3">
    <input type="hidden" name="jaar" value="{{ $jaar }}">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Zoeken op omschrijving…"
           class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-52 focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">Alle statussen</option>
        <option value="ingediend"   @selected(request('status') === 'ingediend')>Ingediend</option>
        <option value="goedgekeurd" @selected(request('status') === 'goedgekeurd')>Goedgekeurd</option>
        <option value="betaald"     @selected(request('status') === 'betaald')>Betaald</option>
        <option value="afgewezen"   @selected(request('status') === 'afgewezen')>Afgewezen</option>
    </select>
    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg">Filteren</button>
    @if (request('search') || request('status'))
        <a href="{{ route('declaraties.index', ['jaar' => $jaar]) }}" class="text-sm text-gray-400 hover:text-gray-600 self-center">Wissen</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if ($declaraties->isEmpty())
        <p class="px-6 py-10 text-center text-sm text-gray-400">Geen declaraties gevonden voor seizoen {{ Seizoen::label($jaar) }}.</p>
    @else
    <table class="min-w-full divide-y divide-gray-100 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Omschrijving</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Categorie</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Datum</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Bedrag</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Bijlage</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Ingediend door</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Status</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($declaraties as $declaratie)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <div class="font-medium text-gray-900">{{ $declaratie->omschrijving }}</div>
                    @if ($declaratie->notities)
                        <div class="text-xs text-gray-400 mt-0.5">{{ Str::limit($declaratie->notities, 60) }}</div>
                    @endif
                </td>
                <td class="px-5 py-3 text-gray-500">{{ $declaratie->categorie ?: '—' }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $declaratie->datum->format('d-m-Y') }}</td>
                <td class="px-5 py-3 font-medium text-gray-800">
                    &euro; {{ number_format($declaratie->bedrag, 2, ',', '.') }}
                </td>
                <td class="px-5 py-3">
                    @if ($declaratie->hasBijlage())
                        <a href="{{ route('declaraties.download', $declaratie) }}"
                           class="inline-flex items-center gap-1 text-xs text-bb-green-700 hover:underline font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            {{ Str::limit($declaratie->bijlage_naam, 20) }}
                        </a>
                    @else
                        <span class="text-xs text-gray-300">—</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-gray-600">{{ $declaratie->creator->name }}</td>
                <td class="px-5 py-3">
                    <form method="POST" action="{{ route('declaraties.status', $declaratie) }}">
                        @csrf @method('PATCH')
                        <select name="status" onchange="this.form.submit()"
                                class="text-xs font-medium rounded-full px-2 py-0.5 border-0 cursor-pointer focus:outline-none {{ $declaratie->statusColor() }}">
                            <option value="ingediend"   @selected($declaratie->status === 'ingediend')>Ingediend</option>
                            <option value="goedgekeurd" @selected($declaratie->status === 'goedgekeurd')>Goedgekeurd</option>
                            <option value="betaald"     @selected($declaratie->status === 'betaald')>Betaald</option>
                            <option value="afgewezen"   @selected($declaratie->status === 'afgewezen')>Afgewezen</option>
                        </select>
                    </form>
                </td>
                <td class="px-5 py-3 text-right">
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('declaraties.edit', $declaratie) }}" class="text-xs text-bb-green-600 hover:underline font-medium">Bewerken</a>
                        <form method="POST" action="{{ route('declaraties.destroy', $declaratie) }}"
                              onsubmit="return confirm('Declaratie verwijderen?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-600">Verwijderen</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-gray-50 border-t border-gray-200">
            <tr>
                <td colspan="3" class="px-5 py-3 text-sm font-semibold text-gray-600">Totaal gefilterd</td>
                <td class="px-5 py-3 text-sm font-bold text-gray-800">
                    &euro; {{ number_format($declaraties->sum('bedrag'), 2, ',', '.') }}
                </td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
    </table>
    @if ($declaraties->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $declaraties->links() }}</div>
    @endif
    @endif
</div>
@endsection
