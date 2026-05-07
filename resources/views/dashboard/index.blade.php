@extends('layouts.app')
@section('title', 'Dashboard — BABB Portaal')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
</div>

@if (auth()->user()->isAdminOrBestuur())
{{-- KPI cards --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Actieve leden</div>
        <div class="text-3xl font-bold text-gray-900">{{ $stats['active_members'] }}</div>
        <div class="text-xs text-gray-400 mt-1">van {{ $stats['total_members'] }} totaal</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Openstaande facturen</div>
        <div class="text-3xl font-bold text-gray-900">{{ $stats['invoices_sent'] }}</div>
        <div class="text-xs text-gray-400 mt-1">{{ $stats['invoices_draft'] }} concept</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="text-xs font-semibold text-red-500 uppercase tracking-wide mb-1">Verlopen facturen</div>
        <div class="text-3xl font-bold text-red-600">{{ $stats['invoices_overdue'] }}</div>
        <div class="text-xs text-gray-400 mt-1">te laat betaald</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Omzet dit jaar</div>
        <div class="text-3xl font-bold text-green-700">&euro; {{ number_format($stats['revenue_ytd'], 2, ',', '.') }}</div>
        <div class="text-xs text-gray-400 mt-1">&euro; {{ number_format($stats['outstanding'], 2, ',', '.') }} uitstaand</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Begroting aankomende events</div>
        <div class="text-3xl font-bold text-blue-700">&euro; {{ number_format($upcomingEventsBudget, 2, ',', '.') }}</div>
        <div class="text-xs text-gray-400 mt-1">totaal geplande begroting</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Recent invoices --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="font-semibold text-gray-800">Recente facturen</h2>
            <a href="{{ route('invoices.index') }}" class="text-xs text-bb-green-600 hover:underline">Alle facturen</a>
        </div>
        <ul class="divide-y divide-gray-100">
            @forelse ($recentInvoices as $invoice)
            <li class="px-5 py-3 flex justify-between items-center text-sm">
                <div>
                    <a href="{{ route('invoices.show', $invoice) }}" class="font-medium text-bb-green-700 hover:underline">{{ $invoice->invoice_number }}</a>
                    <span class="text-gray-500 ml-2">{{ $invoice->member->full_name }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="font-semibold">&euro; {{ number_format($invoice->total, 2, ',', '.') }}</span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $invoice->status === 'draft' ? 'bg-gray-100 text-gray-600' : '' }}
                        {{ $invoice->status === 'sent' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-700' : '' }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
            </li>
            @empty
            <li class="px-5 py-4 text-sm text-gray-400">Geen facturen gevonden.</li>
            @endforelse
        </ul>
    </div>

    {{-- Upcoming events --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="font-semibold text-gray-800">Aankomende evenementen</h2>
            <a href="{{ route('events.index') }}" class="text-xs text-bb-green-600 hover:underline">Alle evenementen</a>
        </div>
        <ul class="divide-y divide-gray-100">
            @forelse ($upcomingEvents as $event)
            <li class="px-5 py-3 text-sm">
                <div class="flex justify-between items-start gap-2">
                    <a href="{{ route('events.show', $event) }}" class="font-medium text-bb-green-700 hover:underline leading-snug">
                        {{ $event->title }}
                    </a>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium shrink-0
                        {{ $event->status === 'bevestigd' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ ucfirst($event->status) }}
                    </span>
                </div>
                <div class="flex items-center gap-3 mt-1 text-gray-400 text-xs">
                    <span>{{ $event->event_date->format('d-m-Y H:i') }}</span>
                    @if ($event->location)
                        <span>. {{ $event->location }}</span>
                    @endif
                    @if ($event->openTasksCount() > 0)
                        <span class="text-orange-500">. {{ $event->openTasksCount() }} open {{ $event->openTasksCount() === 1 ? 'taak' : 'taken' }}</span>
                    @endif
                </div>
            </li>
            @empty
            <li class="px-5 py-4 text-sm text-gray-400">Geen aankomende evenementen.</li>
            @endforelse
        </ul>
    </div>

    {{-- Expiring memberships --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="font-semibold text-gray-800">Lidmaatschappen verlopen binnenkort</h2>
            <a href="{{ route('members.index') }}" class="text-xs text-bb-green-600 hover:underline">Alle leden</a>
        </div>
        <ul class="divide-y divide-gray-100">
            @forelse ($expiringMemberships as $member)
            <li class="px-5 py-3 flex justify-between items-center text-sm">
                <div>
                    <a href="{{ route('members.show', $member) }}" class="font-medium text-bb-green-700 hover:underline">{{ $member->full_name }}</a>
                    <span class="text-gray-400 ml-2 text-xs">{{ $member->membershipType?->name }}</span>
                </div>
                <span class="text-orange-600 font-medium">{{ $member->membership_end->format('d-m-Y') }}</span>
            </li>
            @empty
            <li class="px-5 py-4 text-sm text-gray-400">Geen lidmaatschappen die binnenkort verlopen.</li>
            @endforelse
        </ul>
    </div>
</div>

{{-- Leads widget --}}
@if ($recentLeads->isNotEmpty())
<div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
        <h2 class="font-semibold text-gray-800">Actieve leads</h2>
        <a href="{{ route('leads.index') }}" class="text-xs text-bb-green-600 hover:underline">Alle leads</a>
    </div>
    <ul class="divide-y divide-gray-100">
        @foreach ($recentLeads as $lead)
        <li class="px-5 py-3 flex flex-wrap justify-between items-center gap-2 text-sm">
            <div class="flex items-center gap-3">
                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $lead->statusColor() }}">
                    {{ $lead->statusLabel() }}
                </span>
                <a href="{{ route('leads.show', $lead) }}" class="font-medium text-bb-green-700 hover:underline">
                    {{ $lead->full_name }}
                </a>
                @if ($lead->company_name)
                    <span class="text-gray-400 text-xs hidden sm:inline">{{ $lead->company_name }}</span>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @if ($lead->assignedTo)
                    <span class="text-xs text-gray-500">{{ $lead->assignedTo->name }}</span>
                @endif
                <a href="{{ route('leads.convert-form', $lead) }}"
                   class="text-xs bg-bb-green-600 hover:bg-bb-green-700 text-white px-2.5 py-1 rounded-lg font-medium">
                    Omzetten
                </a>
            </div>
        </li>
        @endforeach
    </ul>
</div>
@endif

@else
{{-- Gebruiker: events only --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 max-w-2xl">
    <div class="px-5 py-4 border-b border-gray-100">
        <h2 class="font-semibold text-gray-800">Aankomende evenementen</h2>
    </div>
    <ul class="divide-y divide-gray-100">
        @forelse ($upcomingEvents as $event)
        <li class="px-5 py-4 text-sm">
            <div class="flex justify-between items-start gap-2 mb-1">
                <span class="font-medium text-gray-900 leading-snug">{{ $event->title }}</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-medium shrink-0
                    {{ $event->status === 'bevestigd' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ ucfirst($event->status) }}
                </span>
            </div>
            <div class="text-gray-500 text-xs flex items-center gap-3">
                <span>{{ $event->event_date->format('d-m-Y H:i') }}</span>
                @if ($event->location)
                    <span>. {{ $event->location }}</span>
                @endif
            </div>
            @if ($event->description)
                <div class="mt-1 text-gray-600 text-xs">{{ Str::limit($event->description, 120) }}</div>
            @endif
        </li>
        @empty
        <li class="px-5 py-6 text-sm text-gray-400 text-center">Geen aankomende evenementen.</li>
        @endforelse
    </ul>
</div>
@endif
@endsection
