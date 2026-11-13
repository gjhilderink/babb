<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Naam <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $membershipType->name ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600"
               placeholder="bijv. Basis, Premium, Corporate">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Prijs per jaar (excl. BTW) <span class="text-red-500">*</span></label>
        <div class="relative">
            <span class="absolute left-3 top-2 text-gray-400 text-sm">&euro;</span>
            <input type="number" name="price_per_year" step="0.01" min="0"
                   value="{{ old('price_per_year', $membershipType->price_per_year ?? '0.00') }}"
                   class="w-full border border-gray-300 rounded-lg pl-7 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Afdracht sponsorcommissie per jaar</label>
        <div class="relative">
            <span class="absolute left-3 top-2 text-gray-400 text-sm">&euro;</span>
            <input type="number" name="afdracht_sponsorcommissie" step="0.01" min="0"
                   value="{{ old('afdracht_sponsorcommissie', $membershipType->afdracht_sponsorcommissie ?? '0.00') }}"
                   class="w-full border border-gray-300 rounded-lg pl-7 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
        <p class="text-xs text-gray-400 mt-1">Bedrag per lid dat naar de club wordt overgemaakt. 0 = geen afdracht.</p>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Afdracht Bonboys per jaar</label>
        <div class="relative">
            <span class="absolute left-3 top-2 text-gray-400 text-sm">&euro;</span>
            <input type="number" name="afdracht_bonboys" step="0.01" min="0"
                   value="{{ old('afdracht_bonboys', $membershipType->afdracht_bonboys ?? '0.00') }}"
                   class="w-full border border-gray-300 rounded-lg pl-7 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
        <p class="text-xs text-gray-400 mt-1">Bedrag per lid dat naar Bonboys wordt afgedragen. 0 = geen afdracht.</p>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Max. aantal medewerkers</label>
        <input type="number" name="max_members" min="1"
               value="{{ old('max_members', $membershipType->max_members ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600"
               placeholder="Leeg = onbeperkt">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Korte omschrijving</label>
        <input type="text" name="description"
               value="{{ old('description', $membershipType->description ?? '') }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600"
               placeholder="Één zin die het pakket omschrijft">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Voordelen</label>
        <textarea name="benefits" rows="5"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600"
                  placeholder="Zet elk voordeel op een nieuwe regel">{{ old('benefits', isset($membershipType) ? implode("\n", $membershipType->benefits ?? []) : '') }}</textarea>
        <p class="text-xs text-gray-400 mt-1">Één voordeel per regel. Deze worden als lijst getoond.</p>
    </div>

    @php
        $prijsRegels = old('prijzen', isset($membershipType)
            ? $membershipType->prijzen->map(fn ($p) => [
                'jaar'                      => $p->jaar,
                'prijs'                     => $p->prijs,
                'afdracht_sponsorcommissie' => $p->afdracht_sponsorcommissie,
                'afdracht_bonboys'          => $p->afdracht_bonboys,
            ])->values()->all()
            : []);
    @endphp
    <div class="md:col-span-2" x-data="{
            regels: @js(array_values($prijsRegels)),
            seizoen(j) { j = +j; return j > 1999 ? String(j % 100).padStart(2, '0') + '/' + String((j + 1) % 100).padStart(2, '0') : ''; }
         }">
        <label class="block text-sm font-medium text-gray-700 mb-1">Prijzen per seizoen</label>
        <p class="text-xs text-gray-400 mb-2">
            Leg vast wat de prijs en de afdrachten in een bepaald seizoen (1 september t/m 31 augustus) waren; jaar 2026 = seizoen 26/27.
            Voor seizoenen zonder regel, of een leeg veld, gelden de standaardbedragen hierboven.
        </p>
        <table class="w-full text-sm" x-show="regels.length">
            <thead>
                <tr class="text-left text-xs text-gray-500">
                    <th class="pb-1 pr-2 font-medium w-28">Jaar (seizoen)</th>
                    <th class="pb-1 pr-2 font-medium">Prijs (excl. BTW)</th>
                    <th class="pb-1 pr-2 font-medium">Afdracht sponsorcommissie</th>
                    <th class="pb-1 pr-2 font-medium">Afdracht Bonboys</th>
                    <th class="pb-1"></th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(regel, i) in regels" :key="i">
                    <tr>
                        <td class="pb-2 pr-2">
                            <input type="number" min="2000" max="2100" step="1" :name="`prijzen[${i}][jaar]`" x-model="regel.jaar"
                                   class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
                            <div class="text-xs text-gray-400 mt-0.5" x-text="seizoen(regel.jaar) ? 'seizoen ' + seizoen(regel.jaar) : ''"></div>
                        </td>
                        <td class="pb-2 pr-2">
                            <div class="relative">
                                <span class="absolute left-2 top-1.5 text-gray-400 text-sm">&euro;</span>
                                <input type="number" step="0.01" min="0" :name="`prijzen[${i}][prijs]`" x-model="regel.prijs" placeholder="standaard"
                                       class="w-full border border-gray-300 rounded-lg pl-6 pr-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
                            </div>
                        </td>
                        <td class="pb-2 pr-2">
                            <div class="relative">
                                <span class="absolute left-2 top-1.5 text-gray-400 text-sm">&euro;</span>
                                <input type="number" step="0.01" min="0" :name="`prijzen[${i}][afdracht_sponsorcommissie]`" x-model="regel.afdracht_sponsorcommissie" placeholder="standaard"
                                       class="w-full border border-gray-300 rounded-lg pl-6 pr-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
                            </div>
                        </td>
                        <td class="pb-2 pr-2">
                            <div class="relative">
                                <span class="absolute left-2 top-1.5 text-gray-400 text-sm">&euro;</span>
                                <input type="number" step="0.01" min="0" :name="`prijzen[${i}][afdracht_bonboys]`" x-model="regel.afdracht_bonboys" placeholder="standaard"
                                       class="w-full border border-gray-300 rounded-lg pl-6 pr-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
                            </div>
                        </td>
                        <td class="pb-2 text-right">
                            <button type="button" @click="regels.splice(i, 1)" class="text-xs text-red-500 hover:underline">Verwijderen</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <button type="button"
                @click="regels.push({ jaar: regels.length ? Math.min(...regels.map(r => +r.jaar || {{ \App\Support\Seizoen::huidig() }})) - 1 : {{ \App\Support\Seizoen::huidig() }}, prijs: '', afdracht_sponsorcommissie: '', afdracht_bonboys: '' })"
                class="mt-1 text-sm text-bb-green-600 hover:underline">+ Jaar toevoegen</button>
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" id="is_active" value="1"
               @checked(old('is_active', $membershipType->is_active ?? true))
               class="rounded border-gray-300">
        <label for="is_active" class="text-sm font-medium text-gray-700">Actief (zichtbaar bij ledenadministratie)</label>
    </div>
</div>
