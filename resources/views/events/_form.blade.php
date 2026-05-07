{{-- Basisgegevens --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="font-semibold text-gray-800 mb-4">Evenementgegevens</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Naam <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $event->title ?? '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600"
                   placeholder="bijv. Nieuwjaarsborrel 2026">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Startdatum en -tijd <span class="text-red-500">*</span></label>
            <input type="datetime-local" name="event_date"
                   value="{{ old('event_date', isset($event) ? $event->event_date?->format('Y-m-d\TH:i') : '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Einddatum en -tijd</label>
            <input type="datetime-local" name="event_end"
                   value="{{ old('event_end', isset($event) ? $event->event_end?->format('Y-m-d\TH:i') : '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Locatie</label>
            <input type="text" name="location" value="{{ old('location', $event->location ?? '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600"
                   placeholder="bijv. Raadzaal, Amsterdam">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @foreach (['concept' => 'Concept', 'bevestigd' => 'Bevestigd', 'afgerond' => 'Afgerond', 'geannuleerd' => 'Geannuleerd'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('status', $event->status ?? 'concept') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Max. deelnemers</label>
            <input type="number" name="max_attendees" min="1"
                   value="{{ old('max_attendees', $event->max_attendees ?? '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600"
                   placeholder="Leeg = onbeperkt">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Begroting</label>
            <div class="relative">
                <span class="absolute left-3 top-2 text-gray-400 text-sm">&euro;</span>
                <input type="number" name="budget" step="0.01" min="0"
                       value="{{ old('budget', $event->budget ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg pl-7 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600"
                       placeholder="0,00">
            </div>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Beschrijving</label>
            <textarea name="description" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600"
                      placeholder="Waar gaat het evenement over?">{{ old('description', $event->description ?? '') }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notities</label>
            <textarea name="notes" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600"
                      placeholder="Interne notities…">{{ old('notes', $event->notes ?? '') }}</textarea>
        </div>
    </div>
</div>

{{-- Taken --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6" x-data="taskForm()">
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-semibold text-gray-800">Wat moet er geregeld worden?</h2>
        <button type="button" @click="add()" class="text-sm text-bb-green-600 hover:text-bb-green-800 font-medium">+ Taak toevoegen</button>
    </div>

    <div class="space-y-3">
        <template x-for="(task, i) in tasks" :key="i">
            <div class="grid grid-cols-12 gap-2 items-start">
                <div class="col-span-12 md:col-span-5">
                    <input type="text" :name="`tasks[${i}][description]`" x-model="task.description"
                           placeholder="Omschrijving taak *"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="col-span-12 md:col-span-3">
                    <select :name="`tasks[${i}][assigned_to]`" x-model="task.assigned_to"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">— Wie doet het? —</option>
                        <template x-for="u in users" :key="u.id">
                            <option :value="u.name" :selected="task.assigned_to === u.name" x-text="u.name"></option>
                        </template>
                    </select>
                </div>
                <div class="col-span-6 md:col-span-2">
                    <input type="date" :name="`tasks[${i}][due_date]`" x-model="task.due_date"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="col-span-5 md:col-span-1">
                    <select :name="`tasks[${i}][status]`" x-model="task.status"
                            class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm">
                        <option value="open">Open</option>
                        <option value="bezig">Bezig</option>
                        <option value="gereed">Gereed</option>
                    </select>
                </div>
                <div class="col-span-1 flex items-center justify-center pt-1">
                    <button type="button" @click="remove(i)" class="text-red-400 hover:text-red-600 text-xl leading-none">&times;</button>
                </div>
            </div>
        </template>

        <template x-if="tasks.length === 0">
            <p class="text-sm text-gray-400 py-2">Nog geen taken. Klik op "+ Taak toevoegen".</p>
        </template>
    </div>
</div>

{{-- Kosten --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" x-data="costForm()">
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-semibold text-gray-800">Kosten</h2>
        <button type="button" @click="add()" class="text-sm text-bb-green-600 hover:text-bb-green-800 font-medium">+ Kostenpost toevoegen</button>
    </div>

    <div class="space-y-3">
        <template x-for="(cost, i) in costs" :key="i">
            <div class="grid grid-cols-12 gap-2 items-start">
                <div class="col-span-12 md:col-span-4">
                    <input type="text" :name="`costs[${i}][description]`" x-model="cost.description"
                           placeholder="Omschrijving *"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="col-span-6 md:col-span-2">
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-400 text-sm">&euro;</span>
                        <input type="number" :name="`costs[${i}][amount]`" x-model="cost.amount"
                               step="0.01" min="0" placeholder="0,00"
                               class="w-full border border-gray-300 rounded-lg pl-7 pr-3 py-2 text-sm text-right">
                    </div>
                </div>
                <div class="col-span-6 md:col-span-2">
                    <input type="text" :name="`costs[${i}][category]`" x-model="cost.category"
                           placeholder="Categorie"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="col-span-6 md:col-span-2">
                    <input type="text" :name="`costs[${i}][paid_by]`" x-model="cost.paid_by"
                           placeholder="Betaald door"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="col-span-5 md:col-span-1">
                    <input type="date" :name="`costs[${i}][paid_at]`" x-model="cost.paid_at"
                           class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm"
                           title="Betaaldatum">
                </div>
                <div class="col-span-1 flex items-center justify-center pt-1">
                    <button type="button" @click="remove(i)" class="text-red-400 hover:text-red-600 text-xl leading-none">&times;</button>
                </div>
            </div>
        </template>

        <template x-if="costs.length === 0">
            <p class="text-sm text-gray-400 py-2">Nog geen kosten vastgelegd.</p>
        </template>

        <template x-if="costs.length > 0">
            <div class="flex justify-end pt-2 border-t border-gray-100 text-sm font-semibold text-gray-700">
                Totaal: &euro; <span x-text="totalFormatted()" class="ml-1"></span>
            </div>
        </template>
    </div>
</div>

@php
    $initialTasks = old('tasks');
    if ($initialTasks === null) {
        $initialTasks = [];
        if (isset($event)) {
            foreach ($event->tasks as $t) {
                $initialTasks[] = [
                    'description' => $t->description,
                    'assigned_to' => $t->assigned_to ?? '',
                    'status'      => $t->status,
                    'due_date'    => $t->due_date ? $t->due_date->format('Y-m-d') : '',
                ];
            }
        }
    }

    $initialCosts = old('costs');
    if ($initialCosts === null) {
        $initialCosts = [];
        if (isset($event)) {
            foreach ($event->costs as $c) {
                $initialCosts[] = [
                    'description' => $c->description,
                    'amount'      => $c->amount,
                    'category'    => $c->category ?? '',
                    'paid_by'     => $c->paid_by ?? '',
                    'paid_at'     => $c->paid_at ? $c->paid_at->format('Y-m-d') : '',
                ];
            }
        }
    }
@endphp

<script>
function taskForm() {
    return {
        tasks: @json($initialTasks),
        users: @json($users->map(fn($u) => ['id' => $u->id, 'name' => $u->name])),
        add()     { this.tasks.push({ description: '', assigned_to: '', status: 'open', due_date: '' }); },
        remove(i) { this.tasks.splice(i, 1); },
    };
}
function costForm() {
    return {
        costs: @json($initialCosts),
        add()     { this.costs.push({ description: '', amount: '', category: '', paid_by: '', paid_at: '' }); },
        remove(i) { this.costs.splice(i, 1); },
        totalFormatted() {
            const t = this.costs.reduce((s, c) => s + (parseFloat(c.amount) || 0), 0);
            return t.toFixed(2).replace('.', ',');
        },
    };
}
</script>
