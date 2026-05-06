<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Naam <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $membershipType->name ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
               placeholder="bijv. Basis, Premium, Corporate">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Prijs per jaar (excl. BTW) <span class="text-red-500">*</span></label>
        <div class="relative">
            <span class="absolute left-3 top-2 text-gray-400 text-sm">&euro;</span>
            <input type="number" name="price_per_year" step="0.01" min="0"
                   value="{{ old('price_per_year', $membershipType->price_per_year ?? '0.00') }}"
                   class="w-full border border-gray-300 rounded-lg pl-7 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Max. aantal medewerkers</label>
        <input type="number" name="max_members" min="1"
               value="{{ old('max_members', $membershipType->max_members ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
               placeholder="Leeg = onbeperkt">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Korte omschrijving</label>
        <input type="text" name="description"
               value="{{ old('description', $membershipType->description ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
               placeholder="Één zin die het pakket omschrijft">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Voordelen</label>
        <textarea name="benefits" rows="5"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
                  placeholder="Zet elk voordeel op een nieuwe regel">{{ old('benefits', isset($membershipType) ? implode("\n", $membershipType->benefits ?? []) : '') }}</textarea>
        <p class="text-xs text-gray-400 mt-1">Één voordeel per regel. Deze worden als lijst getoond.</p>
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               @checked(old('is_active', $membershipType->is_active ?? true))
               class="rounded border-gray-300">
        <label for="is_active" class="text-sm font-medium text-gray-700">Actief (zichtbaar bij ledenadministratie)</label>
    </div>
</div>
