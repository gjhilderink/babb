{{--
    Overzicht van afdrachten per lid op basis van het lidmaatschapspakket.
    Verwacht: $soort, $jaar, $jaren, $regels, $overzicht, $huidig, $indexRoute,
    optioneel $behoudQuery (extra query-parameters die bij jaarwissel behouden blijven).
--}}
@php
    $euro        = fn ($b) => '€ '.number_format($b, 2, ',', '.');
    $behoudQuery = $behoudQuery ?? [];
@endphp

<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <h2 class="text-lg font-semibold text-gray-800">Afdracht per lid (lidmaatschap)</h2>
    <form method="GET" action="{{ route($indexRoute) }}" class="flex items-center gap-2">
        @foreach ($behoudQuery as $naam => $waarde)
            @if (filled($waarde))<input type="hidden" name="{{ $naam }}" value="{{ $waarde }}">@endif
        @endforeach
        <label for="jaar-{{ $soort }}" class="text-sm text-gray-600">Jaar</label>
        <select id="jaar-{{ $soort }}" name="jaar" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            @foreach ($jaren as $j)
                <option value="{{ $j }}" @selected($j === $jaar)>{{ $j }}</option>
            @endforeach
        </select>
    </form>
</div>

{{-- Samenvatting geselecteerd jaar --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="text-xs text-gray-500 mb-1">Totaal {{ $jaar }} ({{ $huidig['aantal'] }} {{ $huidig['aantal'] === 1 ? 'lid' : 'leden' }})</div>
        <div class="text-xl font-bold text-gray-900">{{ $euro($huidig['totaal']) }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="text-xs text-gray-500 mb-1">Overgemaakt</div>
        <div class="text-xl font-bold text-green-600">{{ $euro($huidig['overgemaakt']) }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="text-xs text-gray-500 mb-1">Nog over te maken</div>
        <div class="text-xl font-bold text-orange-500">{{ $euro($huidig['open']) }}</div>
    </div>
</div>

{{-- Regels per lid --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-3 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
        <h3 class="font-semibold text-gray-800">Per lid — {{ $jaar }}</h3>
        @if ($huidig['open'] > 0)
        <form method="POST" action="{{ route('lidmaatschap-afdrachten.markeer-alles', $soort) }}" class="flex flex-wrap items-center gap-2"
              onsubmit="return confirm('Alle openstaande afdrachten over {{ $jaar }} markeren als overgemaakt?')">
            @csrf
            <input type="hidden" name="jaar" value="{{ $jaar }}">
            <input type="date" name="overgemaakt_op" value="{{ now()->format('Y-m-d') }}" required
                   class="border border-gray-300 rounded-lg px-2 py-1 text-sm">
            <button type="submit" class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-3 py-1.5 rounded-lg">
                Alles markeren als overgemaakt
            </button>
        </form>
        @endif
    </div>

    @if ($regels->isEmpty())
        <p class="px-6 py-10 text-center text-sm text-gray-400">
            Geen actieve leden met deze afdracht in {{ $jaar }}.
            Stel het bedrag in bij het <a href="{{ route('membership-types.index') }}" class="text-bb-green-600 hover:underline">pakket</a>.
        </p>
    @else
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-100 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Lid</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600 hidden md:table-cell">Bedrijf</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Pakket</th>
                <th class="px-5 py-3 text-right font-semibold text-gray-600">Bedrag</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Overgemaakt</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($regels as $regel)
            @php $member = $regel['member']; @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <a href="{{ route('members.show', $member) }}" class="text-bb-green-700 hover:underline font-medium">{{ $member->full_name }}</a>
                </td>
                <td class="px-5 py-3 text-gray-600 hidden md:table-cell">{{ $member->company_name ?? '—' }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $regel['pakket'] ?? '—' }}</td>
                <td class="px-5 py-3 text-right text-gray-900 whitespace-nowrap">{{ $euro($regel['bedrag']) }}</td>
                <td class="px-5 py-3">
                    <form method="POST" action="{{ route('lidmaatschap-afdrachten.update', $soort) }}" class="flex flex-wrap items-center gap-2">
                        @csrf @method('PATCH')
                        <input type="hidden" name="member_id" value="{{ $member->id }}">
                        <input type="hidden" name="jaar" value="{{ $jaar }}">
                        @if ($regel['overgemaakt_op'])
                            <input type="hidden" name="overgemaakt" value="0">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                Ja, op {{ $regel['overgemaakt_op']->format('d-m-Y') }}
                            </span>
                            <button type="submit" class="text-xs text-gray-400 hover:text-red-600"
                                    onclick="return confirm('Markering overgemaakt verwijderen?')">Ongedaan maken</button>
                        @else
                            <input type="hidden" name="overgemaakt" value="1">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">Nee</span>
                            <input type="date" name="overgemaakt_op" value="{{ now()->format('Y-m-d') }}"
                                   class="border border-gray-300 rounded-lg px-2 py-0.5 text-xs">
                            <button type="submit" class="text-xs text-bb-green-600 hover:underline font-medium">Markeer overgemaakt</button>
                        @endif
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-gray-50 font-semibold">
            <tr>
                <td class="px-5 py-3" colspan="2">Totaal</td>
                <td class="px-5 py-3 hidden md:table-cell"></td>
                <td class="px-5 py-3 text-right whitespace-nowrap">{{ $euro($huidig['totaal']) }}</td>
                <td class="px-5 py-3"></td>
            </tr>
        </tfoot>
    </table>
    </div>
    @endif
</div>

{{-- Overzicht per jaar --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
    <div class="px-5 py-3 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800">Overzicht per jaar</h3>
    </div>
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-100 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Jaar</th>
                <th class="px-5 py-3 text-right font-semibold text-gray-600">Leden</th>
                <th class="px-5 py-3 text-right font-semibold text-gray-600">Totaal</th>
                <th class="px-5 py-3 text-right font-semibold text-gray-600">Overgemaakt</th>
                <th class="px-5 py-3 text-right font-semibold text-gray-600">Nog over te maken</th>
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($overzicht as $rij)
            <tr class="hover:bg-gray-50 {{ $rij['jaar'] === $jaar ? 'bg-green-50' : '' }}">
                <td class="px-5 py-3">
                    <a href="{{ route($indexRoute, array_filter($behoudQuery, 'filled') + ['jaar' => $rij['jaar']]) }}" class="text-bb-green-700 hover:underline font-medium">{{ $rij['jaar'] }}</a>
                </td>
                <td class="px-5 py-3 text-right text-gray-600">{{ $rij['aantal'] }}</td>
                <td class="px-5 py-3 text-right whitespace-nowrap">{{ $euro($rij['totaal']) }}</td>
                <td class="px-5 py-3 text-right whitespace-nowrap text-green-600">{{ $euro($rij['overgemaakt']) }}</td>
                <td class="px-5 py-3 text-right whitespace-nowrap text-orange-500">{{ $euro($rij['open']) }}</td>
                <td class="px-5 py-3">
                    @if ($rij['aantal'] === 0)
                        <span class="text-xs text-gray-400">—</span>
                    @elseif ($rij['open'] == 0)
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Volledig overgemaakt</span>
                    @elseif ($rij['overgemaakt'] > 0)
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Deels overgemaakt</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">Nog niet overgemaakt</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
