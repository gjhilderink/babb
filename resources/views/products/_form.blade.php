<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Naam <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
        <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Categorie</label>
        <input type="text" name="category" value="{{ old('category', $product->category ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Prijs (excl. BTW) <span class="text-red-500">*</span></label>
        <div class="relative">
            <span class="absolute left-3 top-2 text-gray-400 text-sm">&euro;</span>
            <input type="number" name="price" step="0.01" min="0" value="{{ old('price', $product->price ?? '0.00') }}"
                   class="w-full border border-gray-300 rounded-lg pl-7 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">BTW-percentage <span class="text-red-500">*</span></label>
        <select name="tax_rate" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="0" @selected(old('tax_rate', $product->tax_rate ?? 21) == 0)>0%</option>
            <option value="9" @selected(old('tax_rate', $product->tax_rate ?? 21) == 9)>9%</option>
            <option value="21" @selected(old('tax_rate', $product->tax_rate ?? 21) == 21)>21%</option>
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Omschrijving</label>
        <textarea name="description" rows="3"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">{{ old('description', $product->description ?? '') }}</textarea>
    </div>
    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               @checked(old('is_active', $product->is_active ?? true))
               class="rounded border-gray-300">
        <label for="is_active" class="text-sm font-medium text-gray-700">Actief (beschikbaar op facturen)</label>
    </div>
</div>
