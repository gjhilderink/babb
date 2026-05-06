@extends('layouts.app')
@section('title', 'Evenementen — BABB Portaal')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Evenementen</h1>
    <a href="{{ route('events.create') }}"
       class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + Nieuw evenement
    </a>
</div>

<form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Zoek op naam..."
           class="flex-1 min-w-48 border border-gray-300 rounded-lg px-3 py-2 text-sm">
    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">Alle statussen</option>
        @foreach (['concept' => 'Concept', 'bevestigd' => 'Bevestigd', 'afgerond' => 'Afgerond', 'geannuleerd' => 'Geannuleerd'] as $val => $label)
            <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm px-4 py-2 rounded-lg">Zoeken</button>
    <a href="{{ route('events.index') }}" class="text-sm text-gray-500 px-3 py-2 hover:text-gray-800">Wis filters</a>
</form>

<div class="space-y-4">
    @forelse ($events as $event)
    @php
        $statusColors = [
            'concept'     => 'bg-gray-100 text-gray-600',
            'bevestigd'   => 'bg-blue-100 text-blue-700',
            'afgerond'    => 'bg-green-100 text-green-700',
            'geannuleerd' => 'bg-red-100 text-red-600',
        ];
    @endphp
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex gap-4 items-start">
            <div class="min-w-16 text-center bg-gray-50 border border-bb-green-100 rounded-lg px-3 py-2">
                <div class="text-xs text-bb-green-500 font-medium uppercase">{{ $event->event_date->translatedFormat('M') }}</div>
                <div class="text-2xl font-bold text-bb-green-700 leading-none">{{ $event->event_date->format('d') }}</div>
                <div class="text-xs text-bb-green-500">{{ $event->event_date->format('Y') }}</div>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('events.show', $event) }}" class="text-lg font-semibold text-gray-900 hover:text-bb-green-700">
                        {{ $event->title }}
                    </a>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$event->status] }}">
                        {{ ucfirst($event->status) }}
                    </span>
                </div>
                <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                    @if ($event->location)
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-bb-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                        {{ $event->location }}
                    </span>
                    @endif
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        {{ $event->event_date->format('H:i') }}
                    </span>
                    @if ($event->tasks_count > 0)
                    <span class="flex items-center gap-1 {{ $event->openTasksCount() > 0 ? 'text-orange-500' : 'text-green-600' }}">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $event->openTasksCount() }} open {{ $event->openTasksCount() === 1 ? 'taak' : 'taken' }}
                    </span>
                    @endif
                    @if ($event->costs->count() > 0)
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        &euro; {{ number_format($event->totalCosts(), 2, ',', '.') }}
                    </span>
                    @endif
                </div>
                @if ($event->description)
                    <p class="text-sm text-gray-500 mt-1 line-clamp-1">{{ $event->description }}</p>
                @endif
            </div>
        </div>
        <div class="flex gap-2 shrink-0">
            <a href="{{ route('events.show', $event) }}"
               class="text-sm border border-bb-green-600 hover:bg-bb-green-50 text-bb-green-700 font-medium px-3 py-1.5 rounded-lg">
                Bekijken
            </a>
            <a href="{{ route('events.edit', $event) }}"
               class="text-sm bg-bb-green-600 hover:bg-bb-green-700 text-white px-3 py-1.5 rounded-lg">
                Bewerken
            </a>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-gray-200 p-10 text-center text-gray-400">
        Nog geen evenementen.
        <a href="{{ route('events.create') }}" class="text-bb-green-600 hover:underline ml-1">Maak het eerste evenement aan.</a>
    </div>
    @endforelse
</div>

@if ($events->hasPages())
    <div class="mt-4">{{ $events->links() }}</div>
@endif
@endsection
