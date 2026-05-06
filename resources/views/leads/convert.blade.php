@extends('layouts.app')
@section('title', 'Omzetten naar lid — BABB Portaal')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('leads.show', $lead) }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; {{ $lead->full_name }}</a>
    <h1 class="text-2xl font-bold text-gray-900">Omzetten naar lid</h1>
</div>

<div class="mb-6 bg-bb-green-50 border border-bb-green-600 border-opacity-30 rounded-xl px-5 py-4 text-sm text-bb-green-800">
    Controleer de gegevens en vul het lidmaatschap in. Na het opslaan wordt
    <strong>{{ $lead->full_name }}</strong> aangemaakt als actief lid en de lead gemarkeerd als gewonnen.
</div>

<form method="POST" action="{{ route('leads.convert', $lead) }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Voornaam <span class="text-bb-red-600">*</span></label>
            <input type="text" name="first_name" value="{{ old('first_name', $lead->first_name) }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Achternaam <span class="text-bb-red-600">*</span></label>
            <input type="text" name="last_name" value="{{ old('last_name', $lead->last_name) }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">E-mailadres <span class="text-bb-red-600">*</span></label>
            <input type="email" name="email" value="{{ old('email', $lead->email) }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Telefoon</label>
            <input type="text" name="phone" value="{{ old('phone', $lead->phone) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Bedrijfsnaam</label>
            <input type="text" name="company_name" value="{{ old('company_name', $lead->company_name) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Stad</label>
            <input type="text" name="city" value="{{ old('city') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>

        <div class="md:col-span-2 border-t border-gray-100 pt-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Lidmaatschap</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pakket</label>
                    <select name="membership_type_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">— geen —</option>
                        @foreach ($membershipTypes as $type)
                            <option value="{{ $type->id }}" @selected(old('membership_type_id') == $type->id)>
                                {{ $type->name }} (&euro; {{ number_format($type->price_per_year, 0, ',', '.') }}/jr)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Startdatum</label>
                    <input type="date" name="membership_start" value="{{ old('membership_start', now()->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Einddatum</label>
                    <input type="date" name="membership_end" value="{{ old('membership_end', now()->addYear()->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
                </div>
            </div>
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notities</label>
            <textarea name="notes" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">{{ old('notes', $lead->notes) }}</textarea>
        </div>
    </div>

    <div class="mt-6 flex flex-wrap gap-3">
        <button type="submit"
                class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-semibold px-6 py-2 rounded-lg flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            Omzetten naar lid
        </button>
        <a href="{{ route('leads.show', $lead) }}" class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2">Annuleren</a>
    </div>
</form>
@endsection
