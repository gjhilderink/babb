@extends('layouts.app')
@section('title', 'Vergadernotities – BABB Portaal')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold text-gray-900">Vergadernotities</h1>
    @if(\App\Services\AclService::allowed('meetings.manage'))
    <a href="{{ route('meetings.create') }}"
       class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + Nieuwe vergadering
    </a>
    @endif
</div>

{{-- Filters --}}
<form method="GET" class="mb-5 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Zoeken op titel…"
           class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-56 focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">Alle statussen</option>
        <option value="gepland"     @selected(request('status') === 'gepland')>Gepland</option>
        <option value="afgerond"    @selected(request('status') === 'afgerond')>Afgerond</option>
        <option value="geannuleerd" @selected(request('status') === 'geannuleerd')>Geannuleerd</option>
    </select>
    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg">Filteren</button>
    @if (request('search') || request('status'))
        <a href="{{ route('meetings.index') }}" class="text-sm text-gray-400 hover:text-gray-600 self-center">Wissen</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if ($meetings->isEmpty())
        <p class="px-6 py-10 text-center text-sm text-gray-400">Geen vergaderingen gevonden.</p>
    @else
    <table class="min-w-full divide-y divide-gray-100 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Vergadering</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Datum</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Locatie</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Status</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Notities</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @php
            $statusColors = [
                'gepland'     => 'bg-blue-100 text-blue-700',
                'afgerond'    => 'bg-green-100 text-green-700',
                'geannuleerd' => 'bg-red-100 text-red-600',
            ];
            @endphp
            @foreach ($meetings as $meeting)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-900">
                    <a href="{{ route('meetings.show', $meeting) }}" class="hover:text-bb-green-700">
                        {{ $meeting->title }}
                    </a>
                </td>
                <td class="px-5 py-3 text-gray-600">{{ $meeting->meeting_date->format('d-m-Y H:i') }}</td>
                <td class="px-5 py-3 text-gray-500">{{ $meeting->location ?: '—' }}</td>
                <td class="px-5 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$meeting->status] }}">
                        {{ ucfirst($meeting->status) }}
                    </span>
                </td>
                <td class="px-5 py-3 text-gray-500">{{ $meeting->notes_count }}</td>
                <td class="px-5 py-3 text-right">
                    <a href="{{ route('meetings.show', $meeting) }}"
                       class="text-xs text-bb-green-600 hover:underline font-medium">Bekijken</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if ($meetings->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $meetings->links() }}</div>
    @endif
    @endif
</div>
@endsection
