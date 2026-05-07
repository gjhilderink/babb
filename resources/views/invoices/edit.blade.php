@extends('layouts.app')
@section('title', 'Factuur bewerken — BABB Portaal')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('invoices.show', $invoice) }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Factuur</a>
    <h1 class="text-2xl font-bold text-gray-900">Factuur bewerken <span class="text-gray-400 font-normal text-lg">{{ $invoice->invoice_number }}</span></h1>
</div>

<form method="POST" action="{{ route('invoices.update', $invoice) }}" x-data="invoiceForm()">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Header --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-800 mb-4">Factuurgegevens</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lid <span class="text-red-500">*</span></label>
                        <select name="member_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">— Selecteer lid —</option>
                            @foreach ($members as $m)
                                <option value="{{ $m->id }}" @selected(old('member_id', $invoice->member_id) == $m->id)>
                                    {{ $m->full_name }}{{ $m->company_name ? ' — '.$m->company_name : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Factuurdatum <span class="text-red-500">*</span></label>
                        <input type="date" name="issue_date" value="{{ old('issue_date', $invoice->issue_date->format('Y-m-d')) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vervaldatum <span class="text-red-500">*</span></label>
                        <input type="date" name="due_date" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notities</label>
                        <textarea name="notes" rows="2"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('notes', $invoice->notes) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Line items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-800 mb-4">Factuurregels</h2>

                <template x-for="(item, index) in items" :key="index">
                    <div class="grid grid-cols-12 gap-2 mb-3 items-start">
                        <div class="col-span-12 md:col-span-5">
                            <select :name="`items[${index}][product_id]`" class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm"
                                    @change="fillFromProduct(index, $event)">
                                <option value="">— Vrije regel —</option>
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}" data-price="{{ $p->price }}" data-tax="{{ $p->tax_rate }}" data-name="{{ $p->name }}">
                                        {{ $p->name }} (€ {{ number_format($p->price, 2, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-12 md:col-span-3">
                            <input type="text" :name="`items[${index}][description]`" x-model="item.description"
                                   placeholder="Omschrijving *"
                                   class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm">
                        </div>
                        <div class="col-span-4 md:col-span-1">
                            <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity"
                                   min="0.01" step="0.01" placeholder="Aantal"
                                   class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm text-right">
                        </div>
                        <div class="col-span-4 md:col-span-1">
                            <input type="number" :name="`items[${index}][unit_price]`" x-model="item.unit_price"
                                   min="0" step="0.01" placeholder="Prijs"
                                   class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm text-right">
                        </div>
                        <div class="col-span-3 md:col-span-1">
                            <select :name="`items[${index}][tax_rate]`" x-model="item.tax_rate"
                                    class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm">
                                <option value="0">0%</option>
                                <option value="9">9%</option>
                                <option value="21">21%</option>
                            </select>
                        </div>
                        <div class="col-span-1 flex items-center justify-center pt-1">
                            <button type="button" @click="removeItem(index)" class="text-red-400 hover:text-red-600 text-lg leading-none">&times;</button>
                        </div>
                    </div>
                </template>

                <button type="button" @click="addItem()"
                        class="mt-2 text-sm text-bb-green-600 hover:text-bb-green-800 font-medium">
                    + Regel toevoegen
                </button>
            </div>
        </div>

        {{-- Totals sidebar --}}
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h2 class="font-semibold text-gray-800 mb-4">Totalen</h2>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Subtotaal</dt>
                        <dd class="font-medium">&euro; <span x-text="formatAmount(subtotal())">0,00</span></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">BTW</dt>
                        <dd class="font-medium">&euro; <span x-text="formatAmount(taxTotal())">0,00</span></dd>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-200">
                        <dt class="font-semibold text-gray-900">Totaal</dt>
                        <dd class="font-bold text-lg">&euro; <span x-text="formatAmount(total())">0,00</span></dd>
                    </div>
                </dl>
            </div>
            <button type="submit"
                    class="w-full bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-5 py-3 rounded-lg">
                Wijzigingen opslaan
            </button>
            <a href="{{ route('invoices.show', $invoice) }}" class="block text-center text-sm text-gray-500 hover:text-gray-700">Annuleren</a>
        </div>
    </div>
</form>

@php
$editItems = $invoice->items->map(function($i) {
    return [
        'description' => $i->description,
        'quantity'    => $i->quantity,
        'unit_price'  => $i->unit_price,
        'tax_rate'    => $i->tax_rate,
        'product_id'  => $i->product_id ?? '',
    ];
})->values();
@endphp
<script>
function invoiceForm() {
    return {
        items: @json($editItems),
        addItem() {
            this.items.push({ description: '', quantity: 1, unit_price: 0, tax_rate: 21, product_id: '' });
        },
        removeItem(index) {
            if (this.items.length > 1) this.items.splice(index, 1);
        },
        fillFromProduct(index, event) {
            const opt = event.target.selectedOptions[0];
            if (opt && opt.dataset.price) {
                this.items[index].unit_price  = parseFloat(opt.dataset.price);
                this.items[index].tax_rate    = parseFloat(opt.dataset.tax);
                this.items[index].description = opt.dataset.name;
            }
        },
        subtotal() {
            return this.items.reduce((s, i) => s + (parseFloat(i.quantity)||0) * (parseFloat(i.unit_price)||0), 0);
        },
        taxTotal() {
            return this.items.reduce((s, i) => {
                const line = (parseFloat(i.quantity)||0) * (parseFloat(i.unit_price)||0);
                return s + line * (parseFloat(i.tax_rate)||0) / 100;
            }, 0);
        },
        total() { return this.subtotal() + this.taxTotal(); },
        formatAmount(v) { return v.toFixed(2).replace('.', ','); },
    };
}
</script>
@endsection
