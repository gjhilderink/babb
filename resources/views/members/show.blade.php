@extends('layouts.app')
@section('title', $member->full_name . ' — BABB Portaal')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('members.index') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Leden</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $member->full_name }}</h1>
        <span class="px-2 py-0.5 rounded-full text-xs font-medium
            {{ $member->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
            {{ ucfirst($member->status) }}
        </span>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('invoices.create', ['member_id' => $member->id]) }}"
           class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            + Nieuwe factuur
        </a>
        <a href="{{ route('members.edit', $member) }}"
           class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            Bewerken
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Gegevens</h2>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div><dt class="text-gray-500">E-mail</dt><dd class="font-medium">{{ $member->email }}</dd></div>
                <div><dt class="text-gray-500">Telefoon</dt><dd class="font-medium">{{ $member->phone ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Bedrijf</dt><dd class="font-medium">{{ $member->company_name ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Stad</dt><dd class="font-medium">{{ $member->city ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Lidmaatschap</dt><dd class="font-medium">{{ $member->membershipType?->name ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Looptijd</dt><dd class="font-medium">
                    {{ $member->membership_start?->format('d-m-Y') ?? '—' }} t/m {{ $member->membership_end?->format('d-m-Y') ?? '—' }}
                </dd></div>
            </dl>
            @if ($member->notes)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <dt class="text-gray-500 text-sm mb-1">Notities</dt>
                <dd class="text-sm text-gray-700">{{ $member->notes }}</dd>
            </div>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                <h2 class="font-semibold text-gray-800">Facturen</h2>
                <a href="{{ route('invoices.create', ['member_id' => $member->id]) }}" class="text-xs text-bb-green-600 hover:underline">+ Nieuwe factuur</a>
            </div>
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Factuurnummer</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Datum</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Totaal</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($member->invoices as $invoice)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('invoices.show', $invoice) }}" class="text-bb-green-700 hover:underline">{{ $invoice->invoice_number }}</a>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $invoice->issue_date->format('d-m-Y') }}</td>
                        <td class="px-4 py-3 font-medium">&euro; {{ number_format($invoice->total, 2, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $invoice->status === 'draft' ? 'bg-gray-100 text-gray-600' : '' }}
                                {{ $invoice->status === 'sent' ? 'bg-blue-100 text-blue-700' : '' }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-4 text-center text-gray-400">Geen facturen.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
