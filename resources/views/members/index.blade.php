@extends('layouts.app')
@section('title', 'Leden — BABB Portaal')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Leden</h1>
    <a href="{{ route('members.create') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + Nieuw lid
    </a>
</div>

<form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Zoek op naam, e-mail of bedrijf…"
           class="flex-1 min-w-48 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
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
    <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white text-sm px-4 py-2 rounded-lg">Zoeken</button>
    <a href="{{ route('members.index') }}" class="text-sm text-gray-500 px-3 py-2 hover:text-gray-800">Wis filters</a>
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Naam</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">E-mail</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Bedrijf</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Lidmaatschap</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Verloopt</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($members as $member)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-900">
                    <a href="{{ route('members.show', $member) }}" class="text-indigo-700 hover:underline">{{ $member->full_name }}</a>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $member->email }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $member->company_name ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $member->membershipType?->name ?? '—' }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $member->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $member->status === 'inactive' ? 'bg-gray-100 text-gray-600' : '' }}
                        {{ $member->status === 'suspended' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($member->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $member->membership_end?->format('d-m-Y') ?? '—' }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('members.edit', $member) }}" class="text-xs text-indigo-600 hover:underline">Bewerken</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">Geen leden gevonden.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $members->links() }}
    </div>
</div>
@endsection
