@extends('layouts.app')
@section('title', 'Instellingen — BABB Portaal')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Instellingen</h1>
</div>

<form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-6 max-w-2xl">
    @csrf @method('PUT')

    {{-- Logo --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-800 mb-4">Logo</h2>
        <p class="text-sm text-gray-500 mb-4">
            Wordt links bovenin de navigatiebalk getoond. Aanbevolen: transparant PNG, max. 200&times;60 px.
        </p>

        @if ($logo)
        <div class="mb-4 flex items-center gap-4">
            <img src="{{ asset($logo) }}" alt="Huidig logo" class="h-10 object-contain bg-gray-900 rounded px-3 py-1">
            <label class="flex items-center gap-2 text-sm text-bb-red-600 cursor-pointer">
                <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300">
                Logo verwijderen
            </label>
        </div>
        @endif

        <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $logo ? 'Nieuw logo uploaden' : 'Logo uploaden' }}
        </label>
        <input type="file" name="logo" accept="image/*"
               class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-bb-green-600 file:text-white hover:file:bg-bb-green-700 cursor-pointer">
        <p class="text-xs text-gray-400 mt-1">JPG, PNG of SVG — max. 2 MB</p>
    </div>

    {{-- Achtergrond --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-800 mb-4">Achtergrondafbeelding</h2>
        <p class="text-sm text-gray-500 mb-4">
            Wordt als achtergrond van het portaal getoond, met een licht transparant overlay zodat de inhoud leesbaar blijft.
        </p>

        @if ($background)
        <div class="mb-4">
            <img src="{{ asset($background) }}" alt="Huidige achtergrond"
                 class="w-full max-h-40 object-cover rounded-lg border border-gray-200">
            <label class="flex items-center gap-2 text-sm text-bb-red-600 cursor-pointer mt-2">
                <input type="checkbox" name="remove_background" value="1" class="rounded border-gray-300">
                Achtergrond verwijderen
            </label>
        </div>
        @endif

        <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $background ? 'Nieuwe achtergrond uploaden' : 'Achtergrond uploaden' }}
        </label>
        <input type="file" name="background" accept="image/*"
               class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-bb-green-600 file:text-white hover:file:bg-bb-green-700 cursor-pointer">
        <p class="text-xs text-gray-400 mt-1">JPG of PNG — max. 5 MB. Gebruik een rustige afbeelding voor leesbaarheid.</p>
    </div>

    {{-- Factuur logo --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-800 mb-4">Logo op facturen</h2>
        <p class="text-sm text-gray-500 mb-4">
            Wordt links bovenin op de PDF-factuur getoond. Aanbevolen: PNG of JPG, max. 400&times;120 px.
        </p>

        @if ($invoice_logo)
        <div class="mb-4 flex items-center gap-4">
            <img src="{{ asset($invoice_logo) }}" alt="Huidig factuurlogo" class="h-12 object-contain bg-gray-50 border border-gray-200 rounded px-3 py-1">
            <label class="flex items-center gap-2 text-sm text-bb-red-600 cursor-pointer">
                <input type="checkbox" name="remove_invoice_logo" value="1" class="rounded border-gray-300">
                Logo verwijderen
            </label>
        </div>
        @endif

        <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $invoice_logo ? 'Nieuw factuurlogo uploaden' : 'Factuurlogo uploaden' }}
        </label>
        <input type="file" name="invoice_logo" accept="image/*"
               class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-bb-red-600 file:text-white hover:file:bg-bb-red-700 cursor-pointer">
        <p class="text-xs text-gray-400 mt-1">JPG, PNG of SVG — max. 2 MB</p>
    </div>

    {{-- Bedrijfsgegevens --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-800 mb-4">Bedrijfsgegevens op facturen</h2>
        <p class="text-sm text-gray-500 mb-4">Worden rechts bovenin de PDF-factuur getoond, onder het factuurnummer.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
                <textarea name="company_address" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">{{ old('company_address', $company_address) }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">KvK-nummer</label>
                <input type="text" name="company_kvk" value="{{ old('company_kvk', $company_kvk) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">BTW-nummer</label>
                <input type="text" name="company_vat" value="{{ old('company_vat', $company_vat) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
            </div>
        </div>
    </div>

    {{-- Factuur footer --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-800 mb-4">Footertekst op facturen</h2>
        <p class="text-sm text-gray-500 mb-4">
            Wordt onderaan elke PDF-factuur getoond. Gebruik dit voor bankgegevens, BTW-nummer, KvK-nummer of betalingsinstructies.
        </p>
        <textarea name="invoice_footer" rows="4" placeholder="Bijv.: Betaling binnen 14 dagen op IBAN NL00 BANK 0000 0000 00 t.n.v. BABB. BTW-nr: NL000000000B01. KvK: 00000000."
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">{{ old('invoice_footer', $invoice_footer) }}</textarea>
        <p class="text-xs text-gray-400 mt-1">Max. 1000 tekens.</p>
    </div>

    <div>
        <button type="submit" class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-6 py-2 rounded-lg">
            Instellingen opslaan
        </button>
    </div>
</form>
@endsection
