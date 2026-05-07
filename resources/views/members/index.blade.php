@extends('layouts.app')
@section('title', 'Leden — BABB Portaal')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Leden</h1>
    <div class="flex flex-wrap items-center gap-2">
        {{-- Export --}}
        <a href="{{ route('members.export') }}"
           class="inline-flex items-center gap-1.5 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
            </svg>
            Exporteren
        </a>

        {{-- Import trigger --}}
        <button type="button" onclick="document.getElementById('import-panel').classList.toggle('hidden')"
                class="inline-flex items-center gap-1.5 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 8l5-5 5 5M12 3v12"/>
            </svg>
            Importeren
        </button>

        <a href="{{ route('members.create') }}"
           class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            + Nieuw lid
        </a>
    </div>
</div>

{{-- Import panel --}}
<div id="import-panel" class="hidden mb-6 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
    <h2 class="font-semibold text-gray-800 mb-1">Leden importeren via CSV</h2>
    <p class="text-xs text-gray-500 mb-3">
        Upload een CSV-bestand met als kolomkoppen (puntkomma als scheidingsteken):
        <code class="bg-gray-100 rounded px-1">first_name; last_name; email; phone; company_name; address; postal_code; city; country; membership_type; membership_start; membership_end; status; notes</code><br>
        Bestaande leden worden bijgewerkt op basis van e-mailadres. Datumformat: dd-mm-jjjj of jjjj-mm-dd.
    </p>
    <form method="POST" action="{{ route('members.import') }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
        @csrf
        <input type="file" name="csv_file" accept=".csv,.txt" required
               class="text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-bb-red-600 file:text-white hover:file:bg-bb-red-700 cursor-pointer">
        <button type="submit"
                class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            Importeren
        </button>
    </form>
</div>

<form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Zoek op naam, e-mail of bedrijf..."
           class="flex-1 min-w-0 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">Alle statussen</option>
        <option value="active" @selected(request('status') === 'active')>Actief</option>
        <option value="inactive" @selected(request('status') === 'inactive')>Inactief</option>
        <option value="suspended" @selected(request('status') === 'suspended')>Geschorst</option>
    </select>
    <select name="membership_type_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">Alle lidmaatschappen</option>
        @foreach ($membershipTypes as $type)
            <option value="{{ $type->id }}" @selected(request('membership_type_id') == $type->id)>{{ $type->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm px-4 py-2 rounded-lg">Zoeken</button>
    <a href="{{ route('members.index') }}" class="text-sm text-gray-500 px-3 py-2 hover:text-gray-800">Wis filters</a>
</form>

{{-- Mobile cards --}}
<div class="sm:hidden space-y-3">
    @forelse ($members as $member)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex justify-between items-start gap-2 mb-2">
            <a href="{{ route('members.show', $member) }}" class="font-semibold text-bb-green-700 hover:underline leading-snug">
                {{ $member->full_name }}
            </a>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium shrink-0
                {{ $member->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                {{ $member->status === 'inactive' ? 'bg-gray-100 text-gray-600' : '' }}
                {{ $member->status === 'suspended' ? 'bg-red-100 text-red-700' : '' }}">
                {{ ucfirst($member->status) }}
            </span>
        </div>
        <div class="text-sm text-gray-500 space-y-0.5">
            @if ($member->company_name)<div>{{ $member->company_name }}</div>@endif
            <div>{{ $member->email }}</div>
            @if ($member->membershipType)<div>{{ $member->membershipType->name }}</div>@endif
        </div>
        <div class="mt-3 flex gap-3">
            <a href="{{ route('members.show', $member) }}" class="text-xs text-bb-green-600 font-medium hover:underline">Bekijken</a>
            <a href="{{ route('members.edit', $member) }}" class="text-xs text-bb-green-600 font-medium hover:underline">Bewerken</a>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400">Geen leden gevonden.</div>
    @endforelse
</div>

{{-- Desktop table --}}
<div class="hidden sm:block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Naam</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 hidden lg:table-cell">E-mail</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 hidden md:table-cell">Bedrijf</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Lidmaatschap</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 hidden lg:table-cell">Verloopt</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($members as $member)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">
                        <a href="{{ route('members.show', $member) }}" class="text-bb-green-700 hover:underline">{{ $member->full_name }}</a>
                    </td>
                    <td class="px-4 py-3 text-gray-600 hidden lg:table-cell">{{ $member->email }}</td>
                    <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $member->company_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $member->membershipType?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $member->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $member->status === 'inactive' ? 'bg-gray-100 text-gray-600' : '' }}
                            {{ $member->status === 'suspended' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ ucfirst($member->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600 hidden lg:table-cell">{{ $member->membership_end?->format('d-m-Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('members.edit', $member) }}" class="text-xs text-bb-green-600 hover:underline">Bewerken</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">Geen leden gevonden.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-gray-100">{{ $members->links() }}</div>
</div>

{{-- Mobile pagination --}}
<div class="sm:hidden mt-3">{{ $members->links() }}</div>
@endsection
