@extends('layouts.app')
@section('title', $event->title . ' â€” BABB Portaal')

@section('content')
@php
    $statusColors = [
        'concept'     => 'bg-gray-100 text-gray-600',
        'bevestigd'   => 'bg-blue-100 text-blue-700',
        'afgerond'    => 'bg-green-100 text-green-700',
        'geannuleerd' => 'bg-red-100 text-red-600',
    ];
    $taskColors = [
        'open'   => 'bg-red-100 text-red-600',
        'bezig'  => 'bg-yellow-100 text-yellow-700',
        'gereed' => 'bg-green-100 text-green-700',
    ];
@endphp

<div class="mb-6 flex flex-wrap items-start justify-between gap-3">
    <div class="flex items-center gap-3">
        <a href="{{ route('events.index') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Evenementen</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $event->title }}</h1>
        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$event->status] }}">
            {{ ucfirst($event->status) }}
        </span>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('events.edit', $event) }}"
           class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            Bewerken
        </a>
        <form method="POST" action="{{ route('events.destroy', $event) }}"
              onsubmit="return confirm('Evenement verwijderen?')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="border border-red-300 hover:bg-red-50 text-red-600 text-sm font-medium px-4 py-2 rounded-lg">
                Verwijderen
            </button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Linkerkolom --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Details --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <dl class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm mb-4">
                <div>
                    <dt class="text-gray-500 mb-1">Datum</dt>
                    <dd class="font-medium">{{ $event->event_date->format('d-m-Y') }}</dd>
                    <dd class="text-gray-500">{{ $event->event_date->format('H:i') }}
                        @if ($event->event_end) â€” {{ $event->event_end->format('H:i') }} @endif
                    </dd>
                </div>
                @if ($event->location)
                <div>
                    <dt class="text-gray-500 mb-1">Locatie</dt>
                    <dd class="font-medium">{{ $event->location }}</dd>
                </div>
                @endif
                @if ($event->max_attendees)
                <div>
                    <dt class="text-gray-500 mb-1">Max. deelnemers</dt>
                    <dd class="font-medium">{{ $event->max_attendees }}</dd>
                </div>
                @endif
            </dl>
            @if ($event->description)
            <p class="text-sm text-gray-700 mt-2 pt-4 border-t border-gray-100">{{ $event->description }}</p>
            @endif
            @if ($event->notes)
            <p class="text-xs text-gray-400 mt-3 pt-3 border-t border-gray-100 italic">{{ $event->notes }}</p>
            @endif
        </div>

        {{-- Taken --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-semibold text-gray-800">Wat moet er geregeld worden?</h2>
                <span class="text-xs text-gray-400">
                    {{ $event->tasks->where('status', 'gereed')->count() }} / {{ $event->tasks->count() }} gereed
                </span>
            </div>
            @if ($event->tasks->isEmpty())
                <p class="px-5 py-4 text-sm text-gray-400">Geen taken vastgelegd.</p>
            @else
            <ul class="divide-y divide-gray-100">
                @foreach ($event->tasks as $task)
                <li class="px-5 py-3 flex items-center gap-4">
                    {{-- Status toggle --}}
                    <form method="POST" action="{{ route('event-tasks.status', $task) }}">
                        @csrf @method('PATCH')
                        @php
                            $next = ['open' => 'bezig', 'bezig' => 'gereed', 'gereed' => 'open'];
                        @endphp
                        <input type="hidden" name="status" value="{{ $next[$task->status] }}">
                        <button type="submit" title="Klik om status te wijzigen"
                                class="px-2 py-0.5 rounded-full text-xs font-medium cursor-pointer {{ $taskColors[$task->status] }}">
                            {{ ucfirst($task->status) }}
                        </button>
                    </form>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm {{ $task->status === 'gereed' ? 'line-through text-gray-400' : 'text-gray-800' }}">
                            {{ $task->description }}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-gray-400 shrink-0">
                        @if ($task->assigned_to)
                            <span class="font-medium text-gray-600">{{ $task->assigned_to }}</span>
                        @endif
                        @if ($task->due_date)
                            <span class="{{ $task->due_date->isPast() && $task->status !== 'gereed' ? 'text-red-500 font-medium' : '' }}">
                                {{ $task->due_date->format('d-m-Y') }}
                            </span>
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
            @endif
        </div>

        {{-- Kosten --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-semibold text-gray-800">Kosten</h2>
                @if ($event->costs->count() > 0)
                    <span class="text-sm font-semibold text-gray-700">
                        Totaal: &euro; {{ number_format($event->totalCosts(), 2, ',', '.') }}
                    </span>
                @endif
            </div>
            @if ($event->costs->isEmpty())
                <p class="px-5 py-4 text-sm text-gray-400">Geen kosten vastgelegd.</p>
            @else
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Omschrijving</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Categorie</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-600">Bedrag</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Betaald door</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Betaaldatum</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($event->costs as $cost)
                    <tr>
                        <td class="px-4 py-3 text-gray-800">{{ $cost->description }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $cost->category ?? 'â€”' }}</td>
                        <td class="px-4 py-3 text-right font-medium">&euro; {{ number_format($cost->amount, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $cost->paid_by ?? 'â€”' }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $cost->paid_at ? $cost->paid_at->format('d-m-Y') : 'â€”' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50">
                        <td colspan="2" class="px-4 py-3 font-semibold text-gray-700">Totaal</td>
                        <td class="px-4 py-3 text-right font-bold">&euro; {{ number_format($event->totalCosts(), 2, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
            @endif
        </div>

    </div>

    {{-- Rechterkolom: samenvatting --}}
    <div class="space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-3 text-sm">Overzicht</h3>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Taken open</dt>
                    <dd class="font-medium text-red-600">{{ $event->tasks->whereIn('status', ['open','bezig'])->count() }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Taken gereed</dt>
                    <dd class="font-medium text-green-600">{{ $event->tasks->where('status','gereed')->count() }}</dd>
                </div>
            </dl>
        </div>

        {{-- Budget vs. kosten --}}
        @php
            $totalCosts = $event->totalCosts();
            $budget     = (float) $event->budget;
            $resterend  = $budget - $totalCosts;
            $pct        = $budget > 0 ? min(100, round($totalCosts / $budget * 100)) : 0;
            $overBudget = $budget > 0 && $totalCosts > $budget;
        @endphp

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <h3 class="font-semibold text-gray-800 mb-3 text-sm">Budget</h3>
            <dl class="space-y-2 text-sm">
                @if ($event->budget)
                <div class="flex justify-between">
                    <dt class="text-gray-500">Begroting</dt>
                    <dd class="font-medium">&euro; {{ number_format($budget, 2, ',', '.') }}</dd>
                </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-gray-500">Werkelijke kosten</dt>
                    <dd class="font-medium">&euro; {{ number_format($totalCosts, 2, ',', '.') }}</dd>
                </div>
                @if ($event->budget)
                <div class="flex justify-between pt-2 border-t border-gray-100">
                    <dt class="text-gray-500">{{ $overBudget ? 'Overschrijding' : 'Resterend' }}</dt>
                    <dd class="font-bold {{ $overBudget ? 'text-red-600' : 'text-green-600' }}">
                        &euro; {{ number_format(abs($resterend), 2, ',', '.') }}
                    </dd>
                </div>
                {{-- Voortgangsbalk --}}
                <div class="pt-1">
                    <div class="flex justify-between text-xs text-gray-400 mb-1">
                        <span>{{ $pct }}% gebruikt</span>
                        @if ($overBudget)
                            <span class="text-red-500 font-medium">Over budget</span>
                        @endif
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $overBudget ? 'bg-red-500' : ($pct > 80 ? 'bg-yellow-400' : 'bg-green-500') }}"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endif
            </dl>
        </div>
    </div>
</div>
@endsection

