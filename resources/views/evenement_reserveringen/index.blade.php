@extends('layouts.app')
@use('App\Support\Seizoen')
@section('title', 'Evenementen reservering – BABB Portaal')

@php
    $euro = fn ($b) => '€ '.number_format($b, 2, ',', '.');
    $inputClass = 'border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600';
@endphp

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Evenementen reservering</h1>
    <p class="text-sm text-gray-500 mt-1">
        Bedrag per lid dat elk seizoen (1 september t/m 31 augustus) wordt gereserveerd voor evenementen; jaar 2026 = seizoen 26/27.
        Het totaal is het bedrag per lid × het aantal actieve leden in dat seizoen en staat ook in de
        <a href="{{ route('samenvatting.index') }}" class="text-bb-green-600 hover:underline">samenvatting</a>.
    </p>
</div>

{{-- Nieuwe reservering --}}
<form method="POST" action="{{ route('evenement-reserveringen.store') }}"
      class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 flex flex-wrap items-end gap-3">
    @csrf
    <div x-data="{ j: {{ (int) old('jaar', Seizoen::huidig()) }} }">
        <label class="block text-xs font-medium text-gray-600 mb-1">
            Jaar <span class="text-gray-400" x-text="j > 1999 ? '(seizoen ' + String(j % 100).padStart(2, '0') + '/' + String((j + 1) % 100).padStart(2, '0') + ')' : ''"></span>
        </label>
        <input type="number" name="jaar" min="2000" max="2100" step="1" required x-model.number="j"
               class="{{ $inputClass }} w-28">
    </div>
    <div>
        <label class="block text-xs font-medium text-gray-600 mb-1">Bedrag per lid</label>
        <div class="relative">
            <span class="absolute left-3 top-2 text-gray-400 text-sm">&euro;</span>
            <input type="number" name="bedrag_per_lid" step="0.01" min="0" required
                   value="{{ old('bedrag_per_lid') }}" class="{{ $inputClass }} pl-7 w-36">
        </div>
    </div>
    <div class="flex-1 min-w-[12rem]">
        <label class="block text-xs font-medium text-gray-600 mb-1">Omschrijving</label>
        <input type="text" name="omschrijving" maxlength="255" placeholder="Optioneel"
               value="{{ old('omschrijving') }}" class="{{ $inputClass }} w-full">
    </div>
    <button type="submit" class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + Reservering toevoegen
    </button>
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if ($reserveringen->isEmpty())
        <p class="px-6 py-10 text-center text-sm text-gray-400">Nog geen evenementenreserveringen vastgelegd.</p>
    @else
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-100 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Jaar / seizoen</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Bedrag per lid / omschrijving</th>
                <th class="px-5 py-3 text-right font-semibold text-gray-600">Actieve leden</th>
                <th class="px-5 py-3 text-right font-semibold text-gray-600">Totaal gereserveerd</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($reserveringen as $reservering)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 whitespace-nowrap"><span class="font-medium text-gray-900">{{ $reservering->jaar }}</span> <span class="text-xs text-gray-500">seizoen {{ Seizoen::label($reservering->jaar) }}</span></td>
                <td class="px-5 py-3">
                    <form method="POST" action="{{ route('evenement-reserveringen.update', $reservering) }}" class="flex flex-wrap items-center gap-2">
                        @csrf @method('PUT')
                        <input type="hidden" name="jaar" value="{{ $reservering->jaar }}">
                        <div class="relative">
                            <span class="absolute left-2 top-1.5 text-gray-400 text-sm">&euro;</span>
                            <input type="number" name="bedrag_per_lid" step="0.01" min="0" required value="{{ $reservering->bedrag_per_lid }}"
                                   class="border border-gray-300 rounded-lg pl-6 pr-2 py-1 text-sm w-32">
                        </div>
                        <input type="text" name="omschrijving" maxlength="255" value="{{ $reservering->omschrijving }}" placeholder="Omschrijving"
                               class="border border-gray-300 rounded-lg px-2 py-1 text-sm flex-1 min-w-[10rem]">
                        <button type="submit" class="text-xs text-bb-green-600 hover:underline font-medium">Opslaan</button>
                    </form>
                </td>
                <td class="px-5 py-3 text-right text-gray-600">{{ $reservering->aantal_leden }}</td>
                <td class="px-5 py-3 text-right whitespace-nowrap font-semibold">{{ $euro($reservering->totaal) }}</td>
                <td class="px-5 py-3 text-right">
                    <form method="POST" action="{{ route('evenement-reserveringen.destroy', $reservering) }}"
                          onsubmit="return confirm('Evenementenreservering voor seizoen {{ Seizoen::label($reservering->jaar) }} verwijderen?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-400 hover:text-red-600">Verwijderen</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    @endif
</div>
@endsection
