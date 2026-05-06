@extends('layouts.app')
@section('title', $invoice->invoice_number . ' — BABB Portaal')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('invoices.index') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Facturen</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $invoice->invoice_number }}</h1>
        <span class="px-2 py-0.5 rounded-full text-xs font-medium
            {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : '' }}
            {{ $invoice->status === 'draft' ? 'bg-gray-100 text-gray-600' : '' }}
            {{ $invoice->status === 'sent' ? 'bg-blue-100 text-blue-700' : '' }}
            {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-700' : '' }}">
            {{ ['draft'=>'Concept','sent'=>'Verstuurd','paid'=>'Betaald','overdue'=>'Verlopen','cancelled'=>'Geannuleerd'][$invoice->status] ?? $invoice->status }}
        </span>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('invoices.pdf', $invoice) }}"
           class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg">
            PDF
        </a>
        @if ($invoice->status === 'draft')
        <form method="POST" action="{{ route('invoices.mark-sent', $invoice) }}" class="inline">
            @csrf @method('PATCH')
            <button class="border border-blue-400 hover:bg-blue-50 text-blue-700 text-sm font-medium px-4 py-2 rounded-lg">
                Markeer als verstuurd
            </button>
        </form>
        <a href="{{ route('invoices.edit', $invoice) }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            Bewerken
        </a>
        @endif
        @if (in_array($invoice->status, ['sent', 'overdue']))
        <form method="POST" action="{{ route('invoices.mark-paid', $invoice) }}" class="inline">
            @csrf @method('PATCH')
            <button class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                Markeer als betaald
            </button>
        </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Invoice details --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-6">
                <div>
                    <dt class="text-gray-500 mb-1">Lid</dt>
                    <dd><a href="{{ route('members.show', $invoice->member) }}" class="font-medium text-indigo-700 hover:underline">{{ $invoice->member->full_name }}</a></dd>
                    @if ($invoice->member->company_name)
                        <dd class="text-gray-500 text-xs">{{ $invoice->member->company_name }}</dd>
                    @endif
                </div>
                <div>
                    <dt class="text-gray-500 mb-1">Factuurdatum</dt>
                    <dd class="font-medium">{{ $invoice->issue_date->format('d-m-Y') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500 mb-1">Vervaldatum</dt>
                    <dd class="font-medium {{ $invoice->isOverdue() ? 'text-red-600' : '' }}">{{ $invoice->due_date->format('d-m-Y') }}</dd>
                </div>
                @if ($invoice->paid_at)
                <div>
                    <dt class="text-gray-500 mb-1">Betaald op</dt>
                    <dd class="font-medium text-green-700">{{ $invoice->paid_at->format('d-m-Y') }}</dd>
                </div>
                @endif
            </dl>

            {{-- Line items table --}}
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Omschrijving</th>
                        <th class="px-3 py-2 text-right font-semibold text-gray-600">Aantal</th>
                        <th class="px-3 py-2 text-right font-semibold text-gray-600">Stukprijs</th>
                        <th class="px-3 py-2 text-right font-semibold text-gray-600">BTW</th>
                        <th class="px-3 py-2 text-right font-semibold text-gray-600">Totaal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($invoice->items as $item)
                    <tr>
                        <td class="px-3 py-3">
                            {{ $item->description }}
                            @if ($item->product)
                                <span class="text-gray-400 text-xs ml-1">({{ $item->product->name }})</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 text-right">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right">&euro; {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right text-gray-500">{{ $item->tax_rate }}%</td>
                        <td class="px-3 py-3 text-right font-medium">&euro; {{ number_format($item->line_total + $item->tax_amount, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t-2 border-gray-200">
                    <tr>
                        <td colspan="4" class="px-3 py-2 text-right text-gray-500 text-sm">Subtotaal</td>
                        <td class="px-3 py-2 text-right font-medium">&euro; {{ number_format($invoice->subtotal, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="px-3 py-2 text-right text-gray-500 text-sm">BTW</td>
                        <td class="px-3 py-2 text-right font-medium">&euro; {{ number_format($invoice->tax_amount, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td colspan="4" class="px-3 py-3 text-right font-bold text-gray-900">Totaal</td>
                        <td class="px-3 py-3 text-right font-bold text-lg">&euro; {{ number_format($invoice->total, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            @if ($invoice->notes)
            <div class="mt-4 pt-4 border-t border-gray-100 text-sm text-gray-600">
                <span class="font-medium text-gray-700">Notities:</span> {{ $invoice->notes }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
