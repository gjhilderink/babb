@extends('layouts.app')
@section('title', 'Lidmaatschapspakketten — BABB Portaal')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Lidmaatschapspakketten</h1>
    <a href="{{ route('membership-types.create') }}"
       class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + Nieuw pakket
    </a>
</div>

@if (session('error'))
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse ($types as $type)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
        <div>
            <div class="flex justify-between items-start mb-2">
                <h2 class="text-lg font-bold text-gray-900">{{ $type->name }}</h2>
                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $type->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $type->is_active ? 'Actief' : 'Inactief' }}
                </span>
            </div>

            <div class="text-2xl font-bold text-bb-green-700 mb-1">
                &euro; {{ number_format($type->price_per_year, 2, ',', '.') }}
                <span class="text-sm font-normal text-gray-400">/ jaar</span>
            </div>

            @if ($type->max_members)
                <p class="text-xs text-gray-500 mb-3">Max. {{ $type->max_members }} medewerkers</p>
            @endif

            @if ($type->description)
                <p class="text-sm text-gray-600 mb-4">{{ $type->description }}</p>
            @endif

            @if ($type->benefits && count($type->benefits))
                <ul class="space-y-1 mb-4">
                    @foreach ($type->benefits as $benefit)
                        <li class="flex items-start gap-2 text-sm text-gray-700">
                            <span class="text-green-500 mt-0.5">&#10003;</span>
                            {{ $benefit }}
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-4">
            <span class="text-sm text-gray-500">{{ $type->members_count }} {{ $type->members_count === 1 ? 'lid' : 'leden' }}</span>
            <div class="flex gap-3">
                <a href="{{ route('membership-types.edit', $type) }}"
                   class="text-sm text-bb-green-600 hover:underline">Bewerken</a>
                @if ($type->members_count === 0)
                <form method="POST" action="{{ route('membership-types.destroy', $type) }}"
                      onsubmit="return confirm('Weet je zeker dat je dit pakket wilt verwijderen?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-sm text-red-500 hover:underline">Verwijderen</button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400">
        Nog geen pakketten aangemaakt.
        <a href="{{ route('membership-types.create') }}" class="text-bb-green-600 hover:underline ml-1">Maak het eerste pakket aan.</a>
    </div>
    @endforelse
</div>
@endsection
