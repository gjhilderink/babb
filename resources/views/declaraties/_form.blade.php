<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Omschrijving <span class="text-red-500">*</span></label>
        <input type="text" name="omschrijving" value="{{ old('omschrijving', $declaratie->omschrijving ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600 @error('omschrijving') border-red-400 @enderror">
        @error('omschrijving') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Bedrag (€) <span class="text-red-500">*</span></label>
            <input type="number" name="bedrag" value="{{ old('bedrag', $declaratie->bedrag ?? '') }}"
                   step="0.01" min="0"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600 @error('bedrag') border-red-400 @enderror">
            @error('bedrag') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Datum <span class="text-red-500">*</span></label>
            <input type="date" name="datum" value="{{ old('datum', isset($declaratie) && $declaratie->datum ? $declaratie->datum->format('Y-m-d') : '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Categorie</label>
            <input type="text" name="categorie" value="{{ old('categorie', $declaratie->categorie ?? '') }}"
                   placeholder="bijv. Kantoor, Transport, Marketing"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
                <option value="ingediend"   @selected(old('status', $declaratie->status ?? 'ingediend') === 'ingediend')>Ingediend</option>
                <option value="goedgekeurd" @selected(old('status', $declaratie->status ?? '') === 'goedgekeurd')>Goedgekeurd</option>
                <option value="betaald"     @selected(old('status', $declaratie->status ?? '') === 'betaald')>Betaald</option>
                <option value="afgewezen"   @selected(old('status', $declaratie->status ?? '') === 'afgewezen')>Afgewezen</option>
            </select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Factuur / bon uploaden</label>
        @isset($declaratie)
            @if ($declaratie->hasBijlage())
            <div class="mb-2 flex items-center gap-2 text-sm text-gray-600">
                <svg class="w-4 h-4 text-bb-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                <a href="{{ route('declaraties.download', $declaratie) }}" class="hover:underline text-bb-green-700">{{ $declaratie->bijlage_naam }}</a>
                <span class="text-gray-400">(nieuw bestand vervangt huidige)</span>
            </div>
            @endif
        @endisset
        <input type="file" name="bijlage" accept=".pdf,.jpg,.jpeg,.png,.gif"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600 file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-bb-green-50 file:text-bb-green-700">
        <p class="text-xs text-gray-400 mt-1">PDF, JPG of PNG — max 10 MB</p>
        @error('bijlage') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Notities</label>
        <textarea name="notities" rows="3"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">{{ old('notities', $declaratie->notities ?? '') }}</textarea>
    </div>
</div>
