@extends('layouts.app')
@section('title', 'Nieuwe factuur &mdash; BABB Portaal')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('invoices.index') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Facturen</a>
    <h1 class="text-2xl font-bold text-gray-900">Nieuwe factuur</h1>
</div>

<form method="POST" action="{{ route('invoices.store') }}" x-data="invoiceForm()">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Header --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-800 mb-4">Factuurgegevens</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lid <span class="text-red-500">*</span></label>
                        <select name="member_id" x-model="memberId" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="">&mdash; Selecteer lid &mdash;</option>
                            @foreach ($members as $m)
                                <option value="{{ $m->id }}" @selected(old('member_id', $member?->id) == $m->id)>
                                    {{ $m->full_name }}{{ $m->company_name ? ' &mdash; '.$m->company_name : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                            <input type="checkbox" name="is_membership" value="1" x-model="isMembership"
                                   class="rounded border-gray-300 text-bb-green-600">
                            <span class="font-medium">Lidmaatschapsfactuur</span>
                            <span class="text-gray-500" x-show="isMembership && membership()" x-cloak>
                                &mdash; <span x-text="membership()?.name"></span>
                                (&euro; <span x-text="formatAmount(membership()?.price || 0)"></span> excl. btw)
                            </span>
                        </label>
                        <p x-show="isMembership && memberId && !membership()" x-cloak class="text-xs text-amber-700 mt-1">
                            Dit lid heeft geen lidmaatschapspakket. Stel dit eerst in bij het lid.
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Factuurdatum <span class="text-red-500">*</span></label>
                        <input type="date" name="issue_date" x-model="issueDate"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vervaldatum <span class="text-red-500">*</span></label>
                        <input type="date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notities</label>
                        <textarea name="notes" rows="2" x-model="notes"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
                    </div>
                </div>
            </div>

            {{-- Line items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="font-semibold text-gray-800 mb-4">Factuurregels</h2>

                <template x-for="(item, index) in items" :key="item.uid">
                    <div class="grid grid-cols-12 gap-2 mb-3 items-start">
                        <div class="col-span-12 md:col-span-5">
                            <select :name="`items[${index}][product_id]`" class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm"
                                    @change="fillFromProduct(index, $event)">
                                <option value="">&mdash; Vrije regel &mdash;</option>
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
                                   step="0.01" placeholder="Prijs"
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
                <div x-show="discount()" x-cloak class="mb-4 rounded-lg border border-green-200 bg-green-50 p-3 text-sm">
                    <label class="flex items-start gap-2 cursor-pointer">
                        <input type="hidden" name="apply_discount" value="0">
                        <input type="checkbox" name="apply_discount" value="1" x-model="applyDiscount"
                               class="mt-0.5 rounded border-gray-300 text-bb-green-600">
                        <span>
                            <span class="font-medium text-gray-800">Korting toepassen: <span x-text="discount()?.label"></span></span><br>
                            <span class="text-gray-600" x-text="discount()?.reden"></span>
                            <template x-if="discount() && !discountActive()">
                                <span class="block text-amber-700 text-xs mt-1">Niet geldig op deze factuurdatum &mdash; wordt niet toegepast.</span>
                            </template>
                        </span>
                    </label>
                </div>
                <dl class="space-y-2 text-sm">
                    <template x-for="line in discountLines()">
                        <div class="flex justify-between text-green-700">
                            <dt x-text="line.description"></dt>
                            <dd class="font-medium whitespace-nowrap pl-2">&euro; <span x-text="formatAmount(line.unit_price)"></span></dd>
                        </div>
                    </template>
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
                Factuur aanmaken
            </button>
            <a href="{{ route('invoices.index') }}" class="block text-center text-sm text-gray-500 hover:text-gray-700">Annuleren</a>
        </div>
    </div>
</form>

<script>
function invoiceForm() {
    return {
        uid: 1,
        items: [{ uid: 1, description: '', quantity: 1, unit_price: 0, tax_rate: 21, product_id: '' }],
        memberId: @js((string) old('member_id', $member?->id ?? '')),
        issueDate: @js(old('issue_date', date('Y-m-d'))),
        applyDiscount: @js((bool) old('apply_discount', true)),
        discounts: @json($discounts),
        discount() { return this.discounts[this.memberId] || null; },
        discountActive() {
            const d = this.discount();
            if (!d || !this.issueDate) return false;
            if (d.vanaf && this.issueDate < d.vanaf) return false;
            if (d.tot && this.issueDate > d.tot) return false;
            return true;
        },
        // Spiegelt Member::discountItemsFor() — de server berekent de definitieve regels
        discountLines() {
            const d = this.discount();
            if (!d || !this.applyDiscount || !this.discountActive()) return [];
            const perRate = {};
            this.items.forEach(i => {
                const line = Math.round((parseFloat(i.quantity)||0) * (parseFloat(i.unit_price)||0) * 100) / 100;
                if (line <= 0) return;
                const r = parseFloat(i.tax_rate) || 0;
                perRate[r] = (perRate[r] || 0) + line;
            });
            const rates = Object.keys(perRate);
            if (!rates.length) return [];
            const desc = 'Korting ' + d.label + (d.reden ? ' - ' + d.reden : '');
            const mk = (amount, rate) => ({ description: desc, quantity: 1, unit_price: -amount, tax_rate: parseFloat(rate) });
            if (d.type === 'percentage') {
                const pct = Math.min(100, d.waarde);
                return rates.map(r => mk(Math.round(perRate[r] * pct) / 100, r)).filter(l => l.unit_price < 0);
            }
            const top = rates.sort((a, b) => perRate[b] - perRate[a])[0];
            const amount = Math.round(Math.min(d.waarde, perRate[top]) * 100) / 100;
            return amount > 0 ? [mk(amount, top)] : [];
        },
        allLines() { return this.items.concat(this.discountLines()); },

        // Lidmaatschapsfactuur: pakket van het lid automatisch als eerste regel
        notes: @js(old('notes', '')),
        isMembership: @js((bool) old('is_membership', false)),
        memberships: @json($memberships),
        autoNote: null,
        membership() { return this.memberships[this.memberId] || null; },
        init() {
            this.$watch('memberId', () => this.syncMembership());
            this.$watch('isMembership', () => this.syncMembership());
            this.$watch('issueDate', () => this.syncMembership());
            this.syncMembership();
        },
        syncMembership() {
            // Eerdere automatische regel weghalen
            this.items = this.items.filter(i => !i.membership);

            const pkg  = this.isMembership ? this.membership() : null;
            // Seizoen loopt van 1 september t/m 31 augustus; aangeduid met het startjaar (2026 = 26/27)
            const datum   = this.issueDate ? new Date(this.issueDate) : new Date();
            const seizoen = datum.getMonth() >= 8 ? datum.getFullYear() : datum.getFullYear() - 1;
            const twee    = n => String(n % 100).padStart(2, '0');
            const label   = `seizoen ${twee(seizoen)}/${twee(seizoen + 1)}`;
            const note = pkg ? `Lidmaatschapsfactuur ${pkg.name} — ${label}` : null;

            if (pkg) {
                const prijs = (pkg.prijzen && pkg.prijzen[seizoen] !== undefined) ? pkg.prijzen[seizoen] : pkg.price;
                const line = { uid: ++this.uid, description: `Lidmaatschap ${pkg.name} ${label}`, quantity: 1,
                               unit_price: prijs, tax_rate: 21, product_id: '', membership: true };
                const blank = this.items.length === 1 && !this.items[0].description && !parseFloat(this.items[0].unit_price);
                if (blank) this.items = [line]; else this.items.unshift(line);
            }
            if (!this.items.length) this.addItem();

            // Notitie alleen aanpassen als die leeg is of door ons is ingevuld
            if (!this.notes || this.notes === this.autoNote) this.notes = note || '';
            this.autoNote = note;
        },
        addItem() {
            this.items.push({ uid: ++this.uid, description: '', quantity: 1, unit_price: 0, tax_rate: 21, product_id: '' });
        },
        removeItem(index) {
            if (this.items.length > 1) this.items.splice(index, 1);
        },
        fillFromProduct(index, event) {
            const opt = event.target.selectedOptions[0];
            if (opt && opt.dataset.price) {
                this.items[index].unit_price = parseFloat(opt.dataset.price);
                this.items[index].tax_rate   = parseFloat(opt.dataset.tax);
                this.items[index].description = opt.dataset.name;
            }
        },
        subtotal() {
            return this.allLines().reduce((s, i) => s + (parseFloat(i.quantity)||0) * (parseFloat(i.unit_price)||0), 0);
        },
        taxTotal() {
            return this.allLines().reduce((s, i) => {
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
