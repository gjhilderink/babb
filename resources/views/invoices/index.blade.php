@extends('layouts.app')
@section('title', 'Facturen &mdash; BABB Portaal')

@php
$magVerzenden = \App\Services\AclService::allowed('invoices.edit');
$statusColor = [
    'paid'      => 'bg-green-100 text-green-700',
    'draft'     => 'bg-gray-100 text-gray-600',
    'sent'      => 'bg-blue-100 text-blue-700',
    'overdue'   => 'bg-red-100 text-red-700',
    'cancelled' => 'bg-yellow-100 text-yellow-700',
];
$huidigeStatus = (string) request('status');
$verzendbaar = $invoices->getCollection()->filter->kanVerzonden()->pluck('id')->map(fn ($id) => (string) $id)->values();
@endphp

@section('content')
<div x-data="{ selected: [], alle: @js($verzendbaar),
               get allesGeselecteerd() { return this.alle.length > 0 && this.selected.length === this.alle.length },
               toggleAlles() { this.selected = this.allesGeselecteerd ? [] : [...this.alle] } }">

<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Facturen</h1>
    @if(\App\Services\AclService::allowed('invoices.create'))
    <a href="{{ route('invoices.create') }}"
       class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + Nieuwe factuur
    </a>
    @endif
</div>

{{-- Statusfilter --}}
<div class="flex flex-wrap gap-2 mb-4">
    @foreach (['' => 'Alle'] + $statussen as $val => $label)
        <a href="{{ route('invoices.index', array_filter(['search' => request('search'), 'member_id' => request('member_id'), 'status' => $val])) }}"
           class="px-3 py-1.5 rounded-full text-sm font-medium border transition-colors
                  {{ $huidigeStatus === (string) $val ? 'bg-bb-green-600 border-bb-green-600 text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}">
            {{ $label }}
            <span class="ml-1 text-xs {{ $huidigeStatus === (string) $val ? 'text-white/80' : 'text-gray-400' }}">{{ $aantallen[$val] ?? 0 }}</span>
        </a>
    @endforeach
</div>

<form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 flex flex-wrap gap-3">
    @if ($huidigeStatus !== '')<input type="hidden" name="status" value="{{ $huidigeStatus }}">@endif
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Zoek op factuurnummer of lid..."
           class="flex-1 min-w-0 border border-gray-300 rounded-lg px-3 py-2 text-sm">
    <button type="submit" class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm px-4 py-2 rounded-lg">Zoeken</button>
    <a href="{{ route('invoices.index') }}" class="text-sm text-gray-500 px-3 py-2 hover:text-gray-800">Wis filters</a>
</form>

@if ($magVerzenden)
{{-- Bulk verzenden: checkboxes in de lijst horen via form="bulk-send" bij dit formulier --}}
<form id="bulk-send" method="POST" action="{{ route('invoices.send-bulk') }}"
      @submit="if (! confirm(selected.length + ' factuur/facturen per e-mail verzenden (met PDF als bijlage)?')) $event.preventDefault()">
    @csrf
</form>
<div x-show="selected.length > 0" x-cloak style="display: none"
     class="sticky top-2 z-20 mb-4 flex flex-wrap items-center justify-between gap-3 bg-bb-green-50 border border-green-200 rounded-xl px-4 py-3 shadow-sm">
    <span class="text-sm text-gray-800"><strong x-text="selected.length"></strong> geselecteerd</span>
    <div class="flex items-center gap-3">
        <button type="button" @click="selected = []" class="text-sm text-gray-500 hover:text-gray-800">Selectie wissen</button>
        <button type="submit" form="bulk-send" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            Verzend geselecteerde
        </button>
    </div>
</div>
@endif

{{-- Mobile cards --}}
<div class="sm:hidden space-y-3">
    @forelse ($invoices as $invoice)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 {{ $invoice->isOverdue() ? 'border-l-4 border-l-red-400' : '' }}">
        <div class="flex justify-between items-start gap-2 mb-1">
            <label class="flex items-center gap-2">
                @if ($magVerzenden && $invoice->kanVerzonden())
                <input type="checkbox" form="bulk-send" name="ids[]" value="{{ $invoice->id }}" x-model="selected" class="rounded border-gray-300">
                @endif
                <a href="{{ route('invoices.show', $invoice) }}" class="font-mono text-sm font-semibold text-bb-green-700 hover:underline">
                    {{ $invoice->invoice_number }}
                </a>
            </label>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor[$invoice->isOverdue() ? 'overdue' : $invoice->status] ?? '' }}">
                {{ $statussen[$invoice->isOverdue() ? 'overdue' : $invoice->status] ?? $invoice->status }}
            </span>
        </div>
        <div class="text-sm text-gray-700 font-medium">{{ $invoice->member->full_name }}</div>
        @if ($naar = $invoice->sent_to ?: $invoice->ontvangerEmail())
        <div class="text-xs text-gray-500 break-all">&#9993; {{ $naar }}</div>
        @if ($invoice->sent_to && $invoice->ontvangerEmail() && $invoice->ontvangerEmail() !== $invoice->sent_to)
        <div class="text-xs text-amber-600 break-all">nu: {{ $invoice->ontvangerEmail() }}</div>
        @endif
        @else
        <div class="text-xs text-orange-500">geen e-mailadres</div>
        @endif
        <div class="flex justify-between items-center mt-2 text-sm">
            <span class="text-gray-500">{{ $invoice->issue_date->format('d-m-Y') }}</span>
            <span class="font-semibold">&euro; {{ number_format($invoice->total, 2, ',', '.') }}</span>
        </div>
        @if ($invoice->sent_at)
        <div class="mt-1 text-xs text-gray-400">Gemaild {{ $invoice->sent_at->format('d-m-Y H:i') }} naar {{ $invoice->sent_to }}</div>
        @endif
        <div class="mt-2 flex flex-wrap gap-3">
            <a href="{{ route('invoices.show', $invoice) }}" class="text-xs text-bb-green-600 font-medium hover:underline">Bekijken</a>
            @if ($magVerzenden)
                @include('invoices._send_button', ['invoice' => $invoice])
            @endif
            @if ($invoice->status !== 'paid' && \App\Services\AclService::allowed('invoices.delete'))
            <form method="POST" action="{{ route('invoices.destroy', $invoice) }}"
                  onsubmit="return confirm('Factuur {{ $invoice->invoice_number }} verwijderen? Dit kan niet ongedaan worden gemaakt.')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-bb-red-600 font-medium hover:underline">Verwijderen</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400">Geen facturen gevonden.</div>
    @endforelse
</div>

{{-- Desktop table --}}
<div class="hidden sm:block bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    @if ($magVerzenden)
                    <th class="pl-4 py-3 w-8">
                        <input type="checkbox" :checked="allesGeselecteerd" @change="toggleAlles()" :disabled="alle.length === 0"
                               title="Alle verzendbare facturen op deze pagina selecteren" class="rounded border-gray-300">
                    </th>
                    @endif
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Factuurnummer</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Lid</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 hidden md:table-cell">Datum</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 hidden lg:table-cell">Vervaldatum</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Totaal</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($invoices as $invoice)
                <tr class="hover:bg-gray-50 {{ $invoice->isOverdue() ? 'bg-red-50' : '' }}">
                    @if ($magVerzenden)
                    <td class="pl-4 py-3">
                        @if ($invoice->kanVerzonden())
                        <input type="checkbox" form="bulk-send" name="ids[]" value="{{ $invoice->id }}" x-model="selected" class="rounded border-gray-300">
                        @endif
                    </td>
                    @endif
                    <td class="px-4 py-3 font-mono text-xs">
                        <a href="{{ route('invoices.show', $invoice) }}" class="text-bb-green-700 hover:underline font-medium">{{ $invoice->invoice_number }}</a>
                    </td>
                    <td class="px-4 py-3 text-gray-800">
                        {{ $invoice->member->full_name }}
                        @if ($naar = $invoice->sent_to ?: $invoice->ontvangerEmail())
                        <div class="text-xs text-gray-500 break-all" title="{{ $invoice->sent_to ? 'Verzonden naar' : 'Wordt verzonden naar' }}">&#9993; {{ $naar }}</div>
                        @if ($invoice->sent_to && $invoice->ontvangerEmail() && $invoice->ontvangerEmail() !== $invoice->sent_to)
                        <div class="text-xs text-amber-600 break-all" title="Het factuur-e-mailadres van het lid is gewijzigd sinds het verzenden">nu: {{ $invoice->ontvangerEmail() }}</div>
                        @endif
                        @else
                        <div class="text-xs text-orange-500">geen e-mailadres</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $invoice->issue_date->format('d-m-Y') }}</td>
                    <td class="px-4 py-3 hidden lg:table-cell {{ $invoice->isOverdue() ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                        {{ $invoice->due_date->format('d-m-Y') }}
                    </td>
                    <td class="px-4 py-3 text-right font-medium">&euro; {{ number_format($invoice->total, 2, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor[$invoice->isOverdue() ? 'overdue' : $invoice->status] ?? '' }}">
                            {{ $statussen[$invoice->isOverdue() ? 'overdue' : $invoice->status] ?? $invoice->status }}
                        </span>
                        @if ($invoice->sent_at)
                        <div class="text-xs text-gray-400 mt-1 whitespace-nowrap" title="Naar {{ $invoice->sent_to }}">Gemaild {{ $invoice->sent_at->format('d-m-Y') }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('invoices.show', $invoice) }}" class="text-xs text-bb-green-600 hover:underline mr-3">Bekijken</a>
                        @if ($magVerzenden)
                            <span class="mr-3">@include('invoices._send_button', ['invoice' => $invoice])</span>
                        @endif
                        @if ($invoice->status !== 'paid' && \App\Services\AclService::allowed('invoices.delete'))
                        <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" class="inline"
                              onsubmit="return confirm('Factuur {{ $invoice->invoice_number }} verwijderen? Dit kan niet ongedaan worden gemaakt.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-bb-red-600 hover:underline">Verwijderen</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $magVerzenden ? 8 : 7 }}" class="px-4 py-8 text-center text-gray-400">Geen facturen gevonden.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-gray-100">{{ $invoices->links() }}</div>
</div>

<div class="sm:hidden mt-3">{{ $invoices->links() }}</div>
</div>
@endsection
