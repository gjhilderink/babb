@extends('layouts.app')
@section('title', 'Potentiele leden — BABB Portaal')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Potentiele leden</h1>
    <a href="{{ route('leads.create') }}"
       class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + Nieuwe lead
    </a>
</div>

<form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Zoek op naam, e-mail of bedrijf..."
           class="flex-1 min-w-0 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">Alle statussen</option>
        @foreach (['nieuw'=>'Nieuw','contact'=>'In contact','follow_up'=>'Follow-up','gewonnen'=>'Gewonnen','verloren'=>'Verloren'] as $val => $label)
            <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
        @endforeach
    </select>
    <select name="assigned_to" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">Alle opvolgers</option>
        @foreach ($users as $u)
            <option value="{{ $u->id }}" @selected(request('assigned_to') == $u->id)>{{ $u->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm px-4 py-2 rounded-lg">Zoeken</button>
    <a href="{{ route('leads.index') }}" class="text-sm text-gray-500 px-3 py-2 hover:text-gray-800">Wis filters</a>
</form>

{{-- Mobile cards --}}
<div class="sm:hidden space-y-3">
    @forelse ($leads as $lead)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 {{ $lead->isConverted() ? 'opacity-60' : '' }}">
        <div class="flex justify-between items-start gap-2 mb-2">
            <a href="{{ route('leads.show', $lead) }}" class="font-semibold text-bb-green-700 hover:underline leading-snug">
                {{ $lead->full_name }}
            </a>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium shrink-0 {{ $lead->statusColor() }}">
                {{ $lead->statusLabel() }}
            </span>
        </div>
        <div class="text-sm text-gray-500 space-y-0.5">
            @if ($lead->company_name)<div>{{ $lead->company_name }}</div>@endif
            @if ($lead->assignedTo)<div>Opvolging: {{ $lead->assignedTo->name }}</div>@endif
            @if ($lead->referredByMember)<div>Via: {{ $lead->referredByMember->full_name }}</div>
            @elseif ($lead->referred_by_name)<div>Via: {{ $lead->referred_by_name }}</div>@endif
        </div>
        <div class="mt-3 flex flex-wrap gap-3">
            <a href="{{ route('leads.show', $lead) }}" class="text-xs text-bb-green-600 font-medium hover:underline">Bekijken</a>
            @if (!$lead->isConverted())
            <a href="{{ route('leads.convert-form', $lead) }}" class="text-xs text-white bg-bb-green-600 hover:bg-bb-green-700 font-medium px-2 py-0.5 rounded">
                Omzetten naar lid
            </a>
            @else
            <a href="{{ route('members.show', $lead->member) }}" class="text-xs text-gray-500 hover:underline">
                Lid bekijken
            </a>
            @endif
            <form method="POST" action="{{ route('leads.destroy', $lead) }}"
                  onsubmit="return confirm('{{ $lead->full_name }} verwijderen? Dit kan niet ongedaan worden gemaakt.')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-bb-red-600 font-medium hover:underline">Verwijderen</button>
            </form>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400">Geen leads gevonden.</div>
    @endforelse
</div>

{{-- Desktop table --}}
<div class="hidden sm:block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Naam</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 hidden md:table-cell">Bedrijf</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Aangemeld via</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Opvolging</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($leads as $lead)
                <tr class="hover:bg-gray-50 {{ $lead->isConverted() ? 'opacity-60' : '' }}">
                    <td class="px-4 py-3 font-medium text-gray-900">
                        <a href="{{ route('leads.show', $lead) }}" class="text-bb-green-700 hover:underline">{{ $lead->full_name }}</a>
                        @if ($lead->email)<div class="text-xs text-gray-400 mt-0.5">{{ $lead->email }}</div>@endif
                    </td>
                    <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $lead->company_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">
                        @if ($lead->referredByMember)
                            <a href="{{ route('members.show', $lead->referredByMember) }}" class="text-bb-green-600 hover:underline text-xs">
                                {{ $lead->referredByMember->full_name }}
                            </a>
                        @elseif ($lead->referred_by_name)
                            {{ $lead->referred_by_name }}
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $lead->assignedTo?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $lead->statusColor() }}">
                            {{ $lead->statusLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        @if (!$lead->isConverted())
                        <a href="{{ route('leads.convert-form', $lead) }}"
                           class="text-xs bg-bb-green-600 hover:bg-bb-green-700 text-white font-medium px-2.5 py-1 rounded-lg mr-2">
                            Omzetten
                        </a>
                        @else
                        <a href="{{ route('members.show', $lead->member) }}" class="text-xs text-gray-500 hover:underline mr-2">Lid</a>
                        @endif
                        <a href="{{ route('leads.edit', $lead) }}" class="text-xs text-bb-green-600 hover:underline mr-3">Bewerken</a>
                        <form method="POST" action="{{ route('leads.destroy', $lead) }}" class="inline"
                              onsubmit="return confirm('{{ $lead->full_name }} verwijderen? Dit kan niet ongedaan worden gemaakt.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-bb-red-600 hover:underline">Verwijderen</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">Geen leads gevonden.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-gray-100">{{ $leads->links() }}</div>
</div>

<div class="sm:hidden mt-3">{{ $leads->links() }}</div>
@endsection
