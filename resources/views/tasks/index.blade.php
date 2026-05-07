@extends('layouts.app')
@section('title', 'Taken – BABB Portaal')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold text-gray-900">Taken</h1>
    <a href="{{ route('tasks.create') }}"
       class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + Nieuwe taak
    </a>
</div>

{{-- Filters --}}
<form method="GET" class="mb-5 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Zoeken op titel…"
           class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-52 focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    <select name="user_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">Alle gebruikers</option>
        @foreach ($users as $u)
            <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
        @endforeach
    </select>
    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">Open taken</option>
        <option value="open"   @selected(request('status') === 'open')>Open</option>
        <option value="bezig"  @selected(request('status') === 'bezig')>Bezig</option>
        <option value="gereed" @selected(request('status') === 'gereed')>Gereed</option>
    </select>
    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg">Filteren</button>
    @if (request('search') || request('user_id') || request('status'))
        <a href="{{ route('tasks.index') }}" class="text-sm text-gray-400 hover:text-gray-600 self-center">Wissen</a>
    @endif
</form>

@php
$priorityColors = ['hoog' => 'bg-red-100 text-red-700', 'normaal' => 'bg-yellow-100 text-yellow-700', 'laag' => 'bg-gray-100 text-gray-500'];
$statusColors   = ['open' => 'bg-gray-100 text-gray-600', 'bezig' => 'bg-blue-100 text-blue-700', 'gereed' => 'bg-green-100 text-green-700'];
$nextStatus     = ['open' => 'bezig', 'bezig' => 'gereed', 'gereed' => 'open'];
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if ($tasks->isEmpty() && $eventTasks->isEmpty() && $leads->isEmpty())
        <p class="px-6 py-10 text-center text-sm text-gray-400">Geen open taken gevonden.</p>
    @else
    <table class="min-w-full divide-y divide-gray-100 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Taak</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Toegewezen aan</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Bron</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Prioriteit</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Deadline</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Status</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            {{-- Standalone + meeting tasks --}}
            @foreach ($tasks as $task)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <div class="font-medium text-gray-900">{{ $task->title }}</div>
                    @if ($task->description)
                    <div class="text-xs text-gray-400 mt-0.5">{{ Str::limit($task->description, 70) }}</div>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-bb-green-600 text-white text-xs font-bold flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($task->assignedTo->name, 0, 1)) }}
                        </span>
                        <span class="text-gray-700">{{ $task->assignedTo->name }}</span>
                    </div>
                </td>
                <td class="px-5 py-3">
                    @if ($task->meeting)
                        <a href="{{ route('meetings.show', $task->meeting) }}"
                           class="text-xs bg-purple-50 text-purple-700 px-2 py-0.5 rounded font-medium hover:underline">
                            {{ Str::limit($task->meeting->title, 25) }}
                        </a>
                    @else
                        <span class="text-xs text-gray-400">Algemeen</span>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $priorityColors[$task->priority] }}">
                        {{ ucfirst($task->priority) }}
                    </span>
                </td>
                <td class="px-5 py-3 {{ $task->due_date && $task->due_date->isPast() && $task->status !== 'gereed' ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                    {{ $task->due_date ? $task->due_date->format('d-m-Y') : '—' }}
                </td>
                <td class="px-5 py-3">
                    <form method="POST" action="{{ route('tasks.status', $task) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $nextStatus[$task->status] }}">
                        <button type="submit" title="Klik om status te wijzigen"
                                class="px-2 py-0.5 rounded-full text-xs font-medium cursor-pointer {{ $statusColors[$task->status] }}">
                            {{ ucfirst($task->status) }}
                        </button>
                    </form>
                </td>
                <td class="px-5 py-3 text-right">
                    <a href="{{ route('tasks.edit', $task) }}" class="text-xs text-bb-green-600 hover:underline font-medium">Bewerken</a>
                </td>
            </tr>
            @endforeach

            {{-- Lead opvolging (read-only) --}}
            @foreach ($leads as $lead)
            <tr class="hover:bg-gray-50 bg-blue-50/20">
                <td class="px-5 py-3">
                    <div class="font-medium text-gray-900">
                        <a href="{{ route('leads.show', $lead) }}" class="hover:text-bb-green-700 hover:underline">
                            {{ $lead->full_name }}
                        </a>
                    </div>
                    <div class="text-xs text-gray-500 mt-0.5">{{ Str::limit($lead->action_required, 80) }}</div>
                </td>
                <td class="px-5 py-3">
                    @if ($lead->assignedTo)
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-bb-green-600 text-white text-xs font-bold flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($lead->assignedTo->name, 0, 1)) }}
                        </span>
                        <span class="text-gray-700">{{ $lead->assignedTo->name }}</span>
                    </div>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <a href="{{ route('leads.show', $lead) }}"
                       class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded font-medium hover:underline">
                        Lead
                    </a>
                </td>
                <td class="px-5 py-3"><span class="text-xs text-gray-400">—</span></td>
                <td class="px-5 py-3 text-gray-600">—</td>
                <td class="px-5 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $lead->statusColor() }}">
                        {{ $lead->statusLabel() }}
                    </span>
                </td>
                <td class="px-5 py-3 text-right">
                    <a href="{{ route('leads.show', $lead) }}" class="text-xs text-gray-400 hover:underline">lead</a>
                </td>
            </tr>
            @endforeach

            {{-- Event tasks (read-only) --}}
            @foreach ($eventTasks as $task)
            <tr class="hover:bg-gray-50 bg-orange-50/30">
                <td class="px-5 py-3">
                    <div class="font-medium text-gray-900">{{ $task->description }}</div>
                </td>
                <td class="px-5 py-3">
                    @if ($task->assigned_to)
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-bb-green-600 text-white text-xs font-bold flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($task->assigned_to, 0, 1)) }}
                        </span>
                        <span class="text-gray-700">{{ $task->assigned_to }}</span>
                    </div>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <a href="{{ route('events.show', $task->event) }}"
                       class="text-xs bg-orange-50 text-orange-700 px-2 py-0.5 rounded font-medium hover:underline">
                        {{ Str::limit($task->event->title, 25) }}
                    </a>
                </td>
                <td class="px-5 py-3">
                    <span class="text-xs text-gray-400">—</span>
                </td>
                <td class="px-5 py-3 {{ $task->due_date && $task->due_date->isPast() && $task->status !== 'gereed' ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                    {{ $task->due_date ? $task->due_date->format('d-m-Y') : '—' }}
                </td>
                <td class="px-5 py-3">
                    <form method="POST" action="{{ route('event-tasks.status', $task) }}">
                        @csrf @method('PATCH')
                        @php $etNext = ['open' => 'bezig', 'bezig' => 'gereed', 'gereed' => 'open']; @endphp
                        <input type="hidden" name="status" value="{{ $etNext[$task->status] }}">
                        <button type="submit" title="Klik om status te wijzigen"
                                class="px-2 py-0.5 rounded-full text-xs font-medium cursor-pointer {{ $statusColors[$task->status] }}">
                            {{ ucfirst($task->status) }}
                        </button>
                    </form>
                </td>
                <td class="px-5 py-3 text-right">
                    <a href="{{ route('events.show', $task->event) }}" class="text-xs text-gray-400 hover:underline">evenement</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if ($tasks->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">{{ $tasks->links() }}</div>
    @endif
    @endif
</div>
@endsection
