@extends('layouts.app')
@section('title', 'Financiële samenvatting – BABB Portaal')

@php
    $euro = fn ($b) => ($b < 0 ? '− ' : '').'€ '.number_format(abs($b), 2, ',', '.');
@endphp

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Financiële samenvatting</h1>
        <p class="text-sm text-gray-500 mt-1">Geldstromen per jaar. Bedragen excl. BTW; conceptfacturen en geannuleerde facturen tellen niet mee.</p>
    </div>
    <form method="GET" class="flex items-center gap-2">
        <label for="jaar" class="text-sm text-gray-600">Jaar</label>
        <select id="jaar" name="jaar" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            @foreach ($jaren as $j)
                <option value="{{ $j }}" @selected($j === $jaar)>{{ $j }}</option>
            @endforeach
        </select>
    </form>
</div>

{{-- Kerncijfers --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="text-xs text-gray-500 mb-1">Inkomsten {{ $jaar }}</div>
        <div class="text-xl font-bold text-green-600">{{ $euro($detail['inkomsten']['totaal']) }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="text-xs text-gray-500 mb-1">Af te dragen</div>
        <div class="text-xl font-bold text-orange-500">{{ $euro($detail['totaal_afdrachten']) }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="text-xs text-gray-500 mb-1">Kosten</div>
        <div class="text-xl font-bold text-orange-500">{{ $euro($detail['totaal_kosten']) }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="text-xs text-gray-500 mb-1">Saldo</div>
        <div class="text-xl font-bold {{ $detail['saldo'] < 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $euro($detail['saldo']) }}</div>
    </div>
</div>

{{-- Detail geselecteerd jaar --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
    <div class="px-5 py-3 border-b border-gray-100">
        <h2 class="font-semibold text-gray-800">Geldstromen {{ $jaar }}</h2>
    </div>
    <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-gray-600"></th>
                <th class="px-5 py-3 text-right font-semibold text-gray-600">Totaal</th>
                <th class="px-5 py-3 text-right font-semibold text-gray-600">Ontvangen / betaald</th>
                <th class="px-5 py-3 text-right font-semibold text-gray-600">Nog open</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            {{-- Inkomsten --}}
            <tr class="bg-green-50/60"><td colspan="4" class="px-5 py-2 text-xs font-semibold uppercase tracking-wide text-green-800">Binnenkomend</td></tr>
            <tr>
                <td class="px-5 py-3"><a href="{{ route('invoices.index') }}" class="text-gray-800 hover:underline">Facturen</a></td>
                <td class="px-5 py-3 text-right whitespace-nowrap">{{ $euro($detail['inkomsten']['totaal']) }}</td>
                <td class="px-5 py-3 text-right whitespace-nowrap text-green-600">{{ $euro($detail['inkomsten']['gerealiseerd']) }}</td>
                <td class="px-5 py-3 text-right whitespace-nowrap text-orange-500">{{ $euro($detail['inkomsten']['open']) }}</td>
            </tr>

            {{-- Afdrachten --}}
            <tr class="bg-orange-50/60"><td colspan="4" class="px-5 py-2 text-xs font-semibold uppercase tracking-wide text-orange-800">Af te dragen</td></tr>
            @php $links = ['Afdracht sponsorcommissie' => route('sponsorcommissie.index'), 'Afdracht Bonboys (lidmaatschap)' => route('afdrachten.index'), 'Overige afdrachten Bonboys' => route('afdrachten.index')]; @endphp
            @foreach ($detail['afdrachten'] as $label => $r)
            <tr>
                <td class="px-5 py-3">
                    <a href="{{ $links[$label] }}" class="text-gray-800 hover:underline">{{ $label }}</a>
                    @if ($label === 'Afdracht Bonboys (lidmaatschap)' && $detail['reservering'] > 0)
                        <span class="text-xs text-gray-400">(na reservering van {{ $euro($detail['reservering']) }})</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-right whitespace-nowrap">{{ $euro($r['totaal']) }}</td>
                <td class="px-5 py-3 text-right whitespace-nowrap text-green-600">{{ $euro($r['gerealiseerd']) }}</td>
                <td class="px-5 py-3 text-right whitespace-nowrap text-orange-500">{{ $euro($r['open']) }}</td>
            </tr>
            @endforeach

            {{-- Kosten --}}
            <tr class="bg-orange-50/60"><td colspan="4" class="px-5 py-2 text-xs font-semibold uppercase tracking-wide text-orange-800">Kosten</td></tr>
            @php $links = ['Declaraties (overige kosten)' => route('declaraties.index'), 'Evenementkosten' => route('events.index')]; @endphp
            @foreach ($detail['kosten'] as $label => $r)
            <tr>
                <td class="px-5 py-3"><a href="{{ $links[$label] }}" class="text-gray-800 hover:underline">{{ $label }}</a></td>
                <td class="px-5 py-3 text-right whitespace-nowrap">{{ $euro($r['totaal']) }}</td>
                <td class="px-5 py-3 text-right whitespace-nowrap text-green-600">{{ $euro($r['gerealiseerd']) }}</td>
                <td class="px-5 py-3 text-right whitespace-nowrap text-orange-500">{{ $euro($r['open']) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="border-t-2 border-gray-200">
            <tr class="font-semibold">
                <td class="px-5 py-3">Saldo (inkomsten − afdrachten − kosten)</td>
                <td class="px-5 py-3 text-right whitespace-nowrap {{ $detail['saldo'] < 0 ? 'text-red-600' : '' }}">{{ $euro($detail['saldo']) }}</td>
                <td colspan="2"></td>
            </tr>
            @if ($detail['reservering'] > 0)
            <tr class="text-gray-600">
                <td class="px-5 py-2">
                    Waarvan <a href="{{ route('reserveringen.index') }}" class="hover:underline">gereserveerd</a> voor eigen besteding
                </td>
                <td class="px-5 py-2 text-right whitespace-nowrap">{{ $euro($detail['reservering']) }}</td>
                <td colspan="2"></td>
            </tr>
            @endif
            <tr class="text-gray-500 text-xs">
                <td class="px-5 py-2">BTW op facturen (apart af te dragen aan de Belastingdienst)</td>
                <td class="px-5 py-2 text-right whitespace-nowrap">{{ $euro($detail['btw']) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    </div>
</div>

{{-- Overzicht alle jaren --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-100">
        <h2 class="font-semibold text-gray-800">Overzicht per jaar</h2>
    </div>
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-100 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Jaar</th>
                <th class="px-5 py-3 text-right font-semibold text-gray-600">Inkomsten</th>
                <th class="px-5 py-3 text-right font-semibold text-gray-600">Afdrachten</th>
                <th class="px-5 py-3 text-right font-semibold text-gray-600">Kosten</th>
                <th class="px-5 py-3 text-right font-semibold text-gray-600">Saldo</th>
                <th class="px-5 py-3 text-right font-semibold text-gray-600">Reservering</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($perJaar as $rij)
            <tr class="hover:bg-gray-50 {{ $rij['jaar'] === $jaar ? 'bg-green-50' : '' }}">
                <td class="px-5 py-3">
                    <a href="{{ route('samenvatting.index', ['jaar' => $rij['jaar']]) }}" class="text-bb-green-700 hover:underline font-medium">{{ $rij['jaar'] }}</a>
                </td>
                <td class="px-5 py-3 text-right whitespace-nowrap text-green-600">{{ $euro($rij['inkomsten']['totaal']) }}</td>
                <td class="px-5 py-3 text-right whitespace-nowrap">{{ $euro($rij['totaal_afdrachten']) }}</td>
                <td class="px-5 py-3 text-right whitespace-nowrap">{{ $euro($rij['totaal_kosten']) }}</td>
                <td class="px-5 py-3 text-right whitespace-nowrap font-semibold {{ $rij['saldo'] < 0 ? 'text-red-600' : '' }}">{{ $euro($rij['saldo']) }}</td>
                <td class="px-5 py-3 text-right whitespace-nowrap text-gray-600">{{ $rij['reservering'] > 0 ? $euro($rij['reservering']) : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@endsection
