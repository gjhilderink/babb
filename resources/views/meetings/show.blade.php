@extends('layouts.app')
@section('title', $meeting->title . ' – BABB Portaal')

@section('content')
@php
$statusColors = [
    'gepland'     => 'bg-blue-100 text-blue-700',
    'afgerond'    => 'bg-green-100 text-green-700',
    'geannuleerd' => 'bg-red-100 text-red-600',
];
$myNote = $meeting->noteByUser(auth()->id());
@endphp

<div class="mb-6 flex flex-wrap items-start justify-between gap-3">
    <div class="flex items-center gap-3 flex-wrap">
        <a href="{{ route('meetings.index') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Vergaderingen</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $meeting->title }}</h1>
        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$meeting->status] }}">
            {{ ucfirst($meeting->status) }}
        </span>
    </div>
    @if(\App\Services\AclService::allowed('meetings.manage'))
    <div class="flex gap-2">
        <a href="{{ route('meetings.edit', $meeting) }}"
           class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            Bewerken
        </a>
        <form method="POST" action="{{ route('meetings.destroy', $meeting) }}"
              onsubmit="return confirm('Vergadering verwijderen?')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="border border-red-300 hover:bg-red-50 text-red-600 text-sm font-medium px-4 py-2 rounded-lg">
                Verwijderen
            </button>
        </form>
    </div>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Linker kolom --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Vergaderinfo --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <dl class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500 mb-1">Datum</dt>
                    <dd class="font-medium">{{ $meeting->meeting_date->format('d-m-Y') }}</dd>
                    <dd class="text-gray-500">{{ $meeting->meeting_date->format('H:i') }}</dd>
                </div>
                @if ($meeting->location)
                <div>
                    <dt class="text-gray-500 mb-1">Locatie</dt>
                    <dd class="font-medium">{{ $meeting->location }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-gray-500 mb-1">Aangemaakt door</dt>
                    <dd class="font-medium">{{ $meeting->creator->name }}</dd>
                </div>
            </dl>
            @if ($meeting->agenda)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <dt class="text-sm text-gray-500 mb-2">Agenda</dt>
                <dd class="text-sm text-gray-800 whitespace-pre-line">{{ $meeting->agenda }}</dd>
            </div>
            @endif
        </div>

        {{-- Mijn notitie --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-semibold text-gray-800">Mijn notitie</h2>
                @if ($myNote)
                <form method="POST" action="{{ route('meetings.notes.delete', $meeting) }}"
                      onsubmit="return confirm('Notitie verwijderen?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:text-red-700">Verwijderen</button>
                </form>
                @endif
            </div>
            <div class="p-5">
                <form method="POST" action="{{ route('meetings.notes.save', $meeting) }}">
                    @csrf
                    <textarea name="content" rows="6"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600"
                              placeholder="Schrijf hier je notities voor deze vergadering…">{{ old('content', $myNote?->content ?? '') }}</textarea>
                    <div class="mt-3 flex items-center justify-between">
                        <button type="submit"
                                class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                            Notitie opslaan
                        </button>
                        @if ($myNote)
                        <span class="text-xs text-gray-400">
                            Laatst bijgewerkt: {{ $myNote->updated_at->format('d-m-Y H:i') }}
                        </span>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Alle notities --}}
        @if ($meeting->notes->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-800">Notities van alle deelnemers</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach ($meeting->notes->sortBy(fn($n) => $n->user->name) as $note)
                <div class="px-5 py-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-7 h-7 rounded-full bg-bb-green-600 text-white text-xs font-bold flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($note->user->name, 0, 1)) }}
                        </span>
                        <span class="font-medium text-sm text-gray-800">{{ $note->user->name }}</span>
                        <span class="text-xs text-gray-400 ml-auto">{{ $note->updated_at->format('d-m-Y H:i') }}</span>
                    </div>
                    <p class="text-sm text-gray-700 whitespace-pre-line pl-9">{{ $note->content }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- Rechter kolom --}}
    <div class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-3 text-sm">Overzicht</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Deelnemers met notitie</dt>
                    <dd class="font-medium text-gray-800">{{ $meeting->notes->count() }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Jouw notitie</dt>
                    <dd class="font-medium {{ $myNote ? 'text-green-600' : 'text-gray-400' }}">
                        {{ $myNote ? 'Opgeslagen' : 'Nog niet ingevuld' }}
                    </dd>
                </div>
            </dl>
        </div>

        @if ($meeting->notes->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-3 text-sm">Deelnemers</h3>
            <ul class="space-y-2">
                @foreach ($meeting->notes->sortBy(fn($n) => $n->user->name) as $note)
                <li class="flex items-center gap-2 text-sm">
                    <span class="w-6 h-6 rounded-full bg-bb-green-600 text-white text-xs font-bold flex items-center justify-center shrink-0">
                        {{ strtoupper(substr($note->user->name, 0, 1)) }}
                    </span>
                    <span class="text-gray-700">{{ $note->user->name }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

</div>
@endsection
