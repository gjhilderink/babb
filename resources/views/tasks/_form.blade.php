<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Taak <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $task->title ?? '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600"
                   placeholder="Omschrijving van de taak">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Toegewezen aan <span class="text-red-500">*</span></label>
            <select name="assigned_to_user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
                <option value="">— Kies gebruiker —</option>
                @foreach ($users as $u)
                    <option value="{{ $u->id }}" @selected(old('assigned_to_user_id', $task->assigned_to_user_id ?? '') == $u->id)>
                        {{ $u->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deadline</label>
            <input type="date" name="due_date"
                   value="{{ old('due_date', isset($task) ? ($task->due_date?->format('Y-m-d') ?? '') : '') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prioriteit</label>
            <select name="priority" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
                @foreach (['laag' => 'Laag', 'normaal' => 'Normaal', 'hoog' => 'Hoog'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('priority', $task->priority ?? 'normaal') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
                @foreach (['open' => 'Open', 'bezig' => 'Bezig', 'gereed' => 'Gereed'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('status', $task->status ?? 'open') === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Toelichting</label>
            <textarea name="description" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600"
                      placeholder="Optionele toelichting…">{{ old('description', $task->description ?? '') }}</textarea>
        </div>
    </div>
</div>
