@extends('layouts.app')
@section('title', 'Facturen &mdash; BABB Portaal')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Facturen</h1>
    <a href="{{ route('invoices.create') }}"
       class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + Nieuwe factuur
    </a>
</div>

<form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Zoek op factuurnummer of lid..."
           class="flex-1 min-w-0 border border-gray-300 rounded-lg px-3 py-2 text-sm">
    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">Alle statussen</option>
        @foreach (['draft' => 'Concept', 'sent' => 'Verstuurd', 'paid' => 'Betaald', 'overdue' => 'Verlopen', 'cancelled' => 'Geannuleerd'] as $val => $label)
            <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm px-4 py-2 rounded-lg">Zoeken</button>
    <a href="{{ route('invoices.index') }}" class="text-sm text-gray-500 px-3 py-2 hover:text-gray-800">Wis filters</a>
</form>

@php
$statusLabel = ['draft'=>'Concept','sent'=>'Verstuurd','paid'=>'Betaald','overdue'=>'Verlopen','cancelled'=>'Geannuleerd'];
$statusColor = [
    'paid'      => 'bg-green-100 text-green-700',
    'draft'     => 'bg-gray-100 text-gray-600',
    'sent'      => 'bg-blue-100 text-blue-700',
    'overdue'   => 'bg-red-100 text-red-700',
    'cancelled' => 'bg-yellow-100 text-yellow-700',
];
@endphp

{{-- Mobile cards --}}
<div class="sm:hidden space-y-3">
    @forelse ($invoices as $invoice)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 {{ $invoice->isOverdue() ? 'border-l-4 border-l-red-400' : '' }}">
        <div class="flex justify-between items-start gap-2 mb-1">
            <a href="{{ route('invoices.show', $invoice) }}" class="font-mono text-sm font-semibold text-bb-green-700 hover:underline">
                {{ $invoice->invoice_number }}
            </a>
            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor[$invoice->status] ?? '' }}">
                {{ $statusLabel[$invoice->status] ?? $invoice->status }}
            </span>
        </div>
        <div class="text-sm text-gray-700 font-medium">{{ $invoice->member->full_name }}</div>
        <div class="flex justify-between items-center mt-2 text-sm">
            <span class="text-gray-500">{{ $invoice->issue_date->format('d-m-Y') }}</span>
            <span class="font-semibold">&euro; {{ number_format($invoice->total, 2, ',', '.') }}</span>
        </div>
        <div class="mt-2 flex gap-3">
            <a href="{{ route('invoices.show', $invoice) }}" class="text-xs text-bb-green-600 font-medium hover:underline">Bekijken</a>
            @if ($invoice->status !== 'paid')
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
                    <td class="px-4 py-3 font-mono text-xs">
                        <a href="{{ route('invoices.show', $invoice) }}" class="text-bb-green-700 hover:underline font-medium">{{ $invoice->invoice_number }}</a>
                    </td>
                    <td class="px-4 py-3 text-gray-800">{{ $invoice->member->full_name }}</td>
                    <td class="px-4 py-3 text-gray-600 hidden md:table-cell">{{ $invoice->issue_date->format('d-m-Y') }}</td>
                    <td class="px-4 py-3 hidden lg:table-cell {{ $invoice->isOverdue() ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                        {{ $invoice->due_date->format('d-m-Y') }}
                    </td>
                    <td class="px-4 py-3 text-right font-medium">&euro; {{ number_format($invoice->total, 2, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor[$invoice->status] ?? '' }}">
                            {{ $statusLabel[$invoice->status] ?? $invoice->status }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('invoices.show', $invoice) }}" class="text-xs text-bb-green-600 hover:underline mr-3">Bekijken</a>
                        @if ($invoice->status !== 'paid')
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
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">Geen facturen gevonden.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t border-gray-100">{{ $invoices->links() }}</div>
</div>

<div class="sm:hidden mt-3">{{ $invoices->links() }}</div>
@endsection
