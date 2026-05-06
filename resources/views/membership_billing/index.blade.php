@extends('layouts.app')
@section('title', 'Lidmaatschap factureren — BABB Portaal')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('invoices.index') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Facturen</a>
    <h1 class="text-2xl font-bold text-gray-900">Lidmaatschap factureren</h1>
</div>

<form method="POST" action="{{ route('membership-billing.store') }}" x-data="billingForm()">
    @csrf

    {{-- Datums + acties --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="font-semibold text-gray-800 mb-4">Factuurinstellingen</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Factuurdatum <span class="text-red-500">*</span></label>
                <input type="date" name="issue_date" value="{{ $issueDate }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vervaldatum <span class="text-red-500">*</span></label>
                <input type="date" name="due_date" value="{{ $dueDate }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
            </div>
            <div class="flex items-end">
                <div class="text-sm text-gray-600 bg-indigo-50 border border-indigo-100 rounded-lg px-4 py-2 w-full">
                    <span class="font-semibold text-indigo-700" x-text="selected"></span> leden geselecteerd
                    &mdash; totaal
                    <span class="font-semibold text-indigo-700">&euro; <span x-text="totalFormatted()"></span></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Ledenlijst --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800">Actieve leden met lidmaatschapspakket</h2>
            <div class="flex gap-3 text-sm">
                <button type="button" @click="selectAll()" class="text-indigo-600 hover:underline">Alles selecteren</button>
                <button type="button" @click="selectNone()" class="text-gray-500 hover:underline">Niets selecteren</button>
                <button type="button" @click="selectUnbilled()" class="text-green-600 hover:underline">Nog niet gefactureerd</button>
            </div>
        </div>

        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 w-10"></th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Naam</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Bedrijf</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Pakket</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Bedrag</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Status {{ now()->year }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($members as $member)
                <tr class="hover:bg-gray-50"
                    :class="{ 'bg-indigo-50': isChecked({{ $member->id }}) }">
                    <td class="px-4 py-3 text-center">
                        <input type="checkbox"
                               name="member_ids[]"
                               value="{{ $member->id }}"
                               x-model="checkedIds"
                               data-price="{{ $member->membershipType->price_per_year }}"
                               data-billed="{{ $member->already_invoiced ? '1' : '0' }}"
                               class="rounded border-gray-300 text-indigo-600">
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900">
                        <a href="{{ route('members.show', $member) }}" class="hover:text-indigo-700" target="_blank">
                            {{ $member->full_name }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $member->company_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $member->membershipType->name }}</td>
                    <td class="px-4 py-3 text-right font-medium">
                        &euro; {{ number_format($member->membershipType->price_per_year, 2, ',', '.') }}
                    </td>
                    <td class="px-4 py-3">
                        @if ($member->already_invoiced)
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                Al gefactureerd
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                Nog niet gefactureerd
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                        Geen actieve leden met een lidmaatschapspakket gevonden.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @error('member_ids')
        <p class="text-red-500 text-sm mb-4">{{ $message }}</p>
    @enderror

    <div class="flex gap-3">
        <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg"
                :disabled="selected === 0"
                :class="{ 'opacity-50 cursor-not-allowed': selected === 0 }">
            Facturen aanmaken (<span x-text="selected"></span>)
        </button>
        <a href="{{ route('invoices.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2">Annuleren</a>
    </div>
</form>

<script>
function billingForm() {
    const checkboxData = () => {
        const boxes = document.querySelectorAll('input[name="member_ids[]"]');
        return Array.from(boxes).map(b => ({
            id: b.value,
            price: parseFloat(b.dataset.price),
            billed: b.dataset.billed === '1',
        }));
    };

    return {
        checkedIds: [],

        get selected() { return this.checkedIds.length; },

        isChecked(id) { return this.checkedIds.includes(String(id)); },

        totalFormatted() {
            const data = checkboxData();
            const total = data
                .filter(d => this.checkedIds.includes(d.id))
                .reduce((s, d) => s + d.price * 1.21, 0);
            return total.toFixed(2).replace('.', ',');
        },

        selectAll() {
            this.checkedIds = checkboxData().map(d => d.id);
        },

        selectNone() {
            this.checkedIds = [];
        },

        selectUnbilled() {
            this.checkedIds = checkboxData().filter(d => !d.billed).map(d => d.id);
        },
    };
}
</script>
@endsection
