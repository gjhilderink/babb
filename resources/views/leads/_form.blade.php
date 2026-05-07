@php
$statuses = ['nieuw'=>'Nieuw','contact'=>'In contact','follow_up'=>'Follow-up nodig','gewonnen'=>'Gewonnen','verloren'=>'Verloren'];
$sources  = ['Doorverwijzing','Evenement','Website','LinkedIn','Anders'];
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Voornaam <span class="text-bb-red-600">*</span></label>
        <input type="text" name="first_name" value="{{ old('first_name', $lead->first_name ?? '') }}" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Achternaam <span class="text-bb-red-600">*</span></label>
        <input type="text" name="last_name" value="{{ old('last_name', $lead->last_name ?? '') }}" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">E-mailadres</label>
        <input type="email" name="email" value="{{ old('email', $lead->email ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Telefoon</label>
        <input type="text" name="phone" value="{{ old('phone', $lead->phone ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Bedrijfsnaam</label>
        <input type="text" name="company_name" value="{{ old('company_name', $lead->company_name ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-bb-red-600">*</span></label>
        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
            @foreach ($statuses as $val => $label)
                <option value="{{ $val }}" @selected(old('status', $lead->status ?? 'nieuw') === $val)>{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="md:col-span-2 border-t border-gray-100 pt-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Herkomst &amp; opvolging</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kanaal / bron</label>
                <input type="text" name="source" value="{{ old('source', $lead->source ?? '') }}"
                       list="sources-list"
                       placeholder="bijv. Evenement, LinkedIn..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
                <datalist id="sources-list">
                    @foreach ($sources as $s)<option value="{{ $s }}">@endforeach
                </datalist>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Aangemeld door (lid)</label>
                <select name="referred_by_member_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
                    <option value="">— selecteer lid —</option>
                    @foreach ($members as $m)
                        <option value="{{ $m->id }}" @selected(old('referred_by_member_id', $lead->referred_by_member_id ?? null) == $m->id)>
                            {{ $m->full_name }}@if($m->company_name) ({{ $m->company_name }})@endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Aangemeld door (naam, als geen lid)</label>
                <input type="text" name="referred_by_name" value="{{ old('referred_by_name', $lead->referred_by_name ?? '') }}"
                       placeholder="Naam van de aanmelder..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Opvolging door</label>
                <select name="assigned_to_user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
                    <option value="">— selecteer gebruiker —</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}" @selected(old('assigned_to_user_id', $lead->assigned_to_user_id ?? null) == $u->id)>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Wat moet er geregeld worden?</label>
                <textarea name="action_required" rows="2"
                          placeholder="Beschrijf de actie die ondernomen moet worden..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">{{ old('action_required', $lead->action_required ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Notities</label>
        <textarea name="notes" rows="4"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">{{ old('notes', $lead->notes ?? '') }}</textarea>
    </div>
</div>
