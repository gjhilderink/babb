<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h2 class="font-semibold text-gray-800 mb-4">Vergadergegevens</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Titel <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $meeting->title ?? '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600"
                   placeholder="bijv. Bestuursvergadering mei 2026">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Datum en tijd <span class="text-red-500">*</span></label>
            <input type="datetime-local" name="meeting_date"
                   value="{{ old('meeting_date', isset($meeting) ? $meeting->meeting_date?->format('Y-m-d\TH:i') : '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Locatie</label>
            <input type="text" name="location" value="{{ old('location', $meeting->location ?? '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600"
                   placeholder="bijv. Bestuurskamer">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @foreach (['gepland' => 'Gepland', 'afgerond' => 'Afgerond', 'geannuleerd' => 'Geannuleerd'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('status', $meeting->status ?? 'gepland') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Agenda / Onderwerpen</label>
            <textarea name="agenda" rows="5"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600"
                      placeholder="Welke onderwerpen staan op de agenda?">{{ old('agenda', $meeting->agenda ?? '') }}</textarea>
        </div>
    </div>
</div>
