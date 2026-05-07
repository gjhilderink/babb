<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Voornaam <span class="text-red-500">*</span></label>
        <input type="text" name="first_name" value="{{ old('first_name', $member->first_name ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Achternaam <span class="text-red-500">*</span></label>
        <input type="text" name="last_name" value="{{ old('last_name', $member->last_name ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">E-mailadres <span class="text-red-500">*</span></label>
        <input type="email" name="email" value="{{ old('email', $member->email ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Factuur e-mailadres</label>
        <input type="email" name="factuur_email" value="{{ old('factuur_email', $member->factuur_email ?? '') }}"
               placeholder="Leeg = zelfde als e-mailadres"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Telefoonnummer</label>
        <input type="text" name="phone" value="{{ old('phone', $member->phone ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Bedrijfsnaam</label>
        <input type="text" name="company_name" value="{{ old('company_name', $member->company_name ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
        <input type="text" name="address" value="{{ old('address', $member->address ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Postcode</label>
        <input type="text" name="postal_code" value="{{ old('postal_code', $member->postal_code ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Stad</label>
        <input type="text" name="city" value="{{ old('city', $member->city ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    {{-- Privé adres --}}
    <div class="md:col-span-2 border-t border-gray-100 pt-4 mt-2">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Privé adres</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Straat &amp; huisnummer</label>
                <input type="text" name="prive_adres" value="{{ old('prive_adres', $member->prive_adres ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Postcode</label>
                <input type="text" name="prive_postcode" value="{{ old('prive_postcode', $member->prive_postcode ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stad</label>
                <input type="text" name="prive_stad" value="{{ old('prive_stad', $member->prive_stad ?? '') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
            </div>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Lidmaatschapstype</label>
        <select name="membership_type_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">— Geen —</option>
            @foreach ($membershipTypes as $type)
                <option value="{{ $type->id }}" @selected(old('membership_type_id', $member->membership_type_id ?? '') == $type->id)>
                    {{ $type->name }} (€ {{ number_format($type->price_per_year, 2, ',', '.') }}/jaar)
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="active" @selected(old('status', $member->status ?? 'active') === 'active')>Actief</option>
            <option value="inactive" @selected(old('status', $member->status ?? '') === 'inactive')>Inactief</option>
            <option value="suspended" @selected(old('status', $member->status ?? '') === 'suspended')>Geschorst</option>
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Startdatum lidmaatschap</label>
        <input type="date" name="membership_start" value="{{ old('membership_start', isset($member) ? $member->membership_start?->format('Y-m-d') : '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Einddatum lidmaatschap</label>
        <input type="date" name="membership_end" value="{{ old('membership_end', isset($member) ? $member->membership_end?->format('Y-m-d') : '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Notities</label>
        <textarea name="notes" rows="3"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">{{ old('notes', $member->notes ?? '') }}</textarea>
    </div>
</div>
