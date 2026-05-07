<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Onderwerp <span class="text-red-500">*</span></label>
        <input type="text" name="onderwerp" value="{{ old('onderwerp', $afdracht->onderwerp ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600 @error('onderwerp') border-red-400 @enderror">
        @error('onderwerp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Bedrag (€) <span class="text-red-500">*</span></label>
        <input type="number" name="bedrag" value="{{ old('bedrag', $afdracht->bedrag ?? '') }}"
               step="0.01" min="0"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600 @error('bedrag') border-red-400 @enderror">
        @error('bedrag') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Datum</label>
        <input type="date" name="datum" value="{{ old('datum', isset($afdracht) && $afdracht->datum ? $afdracht->datum->format('Y-m-d') : '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
        <select name="status"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
            <option value="nieuw"          @selected(old('status', $afdracht->status ?? 'nieuw') === 'nieuw')>Nieuw</option>
            <option value="nog_te_betalen" @selected(old('status', $afdracht->status ?? '') === 'nog_te_betalen')>Nog te betalen</option>
            <option value="betaald"        @selected(old('status', $afdracht->status ?? '') === 'betaald')>Betaald</option>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Notities</label>
        <textarea name="notities" rows="3"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">{{ old('notities', $afdracht->notities ?? '') }}</textarea>
    </div>
</div>
