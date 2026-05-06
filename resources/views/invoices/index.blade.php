@extends('layouts.app')
@section('title', 'Facturen — BABB Portaal')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Facturen</h1>
    <a href="{{ route('invoices.create') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + Nieuwe factuur
    </a>
</div>

<form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Zoek op factuurnummer of lid…"
           class="flex-1 min-w-48 border border-gray-300 rounded-lg px-3 py-2 text-sm">
    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        <option value="">Alle statussen</option>
        @foreach (['draft' => 'Concept', 'sent' => 'Verstuurd', 'paid' => 'Betaald', 'overdue' => 'Verlopen', 'cancelled' => 'Geannuleerd'] as $val => $label)
            <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white text-sm px-4 py-2 rounded-lg">Zoeken</button>
    <a href="{{ route('invoices.index') }}" class="text-sm text-gray-500 px-3 py-2 hover:text-gray-800">Wis filters</a>
</form>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Factuurnummer</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Lid</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Datum</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Vervaldatum</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-600">Totaal</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($invoices as $invoice)
            <tr class="hover:bg-gray-50 {{ $invoice->isOverdue() ? 'bg-red-50' : '' }}">
                <td class="px-4 py-3 font-mono text-xs">
                    <a href="{{ route('invoices.show', $invoice) }}" class="text-indigo-700 hover:underline font-medium">{{ $invoice->invoice_number }}</a>
                </td>
                <td class="px-4 py-3 text-gray-800">{{ $invoice->member->full_name }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $invoice->issue_date->format('d-m-Y') }}</td>
                <td class="px-4 py-3 text-gray-600 {{ $invoice->isOverdue() ? 'text-red-600 font-medium' : '' }}">
                    {{ $invoice->due_date->format('d-m-Y') }}
                </td>
                <td class="px-4 py-3 text-right font-medium">&euro; {{ number_format($invoice->total, 2, ',', '.') }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $invoice->status === 'draft' ? 'bg-gray-100 text-gray-600' : '' }}
                        {{ $invoice->status === 'sent' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $invoice->status === 'cancelled' ? 'bg-yellow-100 text-yellow-700' : '' }}">
                        {{ ['draft'=>'Concept','sent'=>'Verstuurd','paid'=>'Betaald','overdue'=>'Verlopen','cancelled'=>'Geannuleerd'][$invoice->status] ?? $invoice->status }}
                    </span>
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('invoices.show', $invoice) }}" class="text-xs text-indigo-600 hover:underline">Bekijken</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">Geen facturen gevonden.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">{{ $invoices->links() }}</div>
</div>
@endsection
