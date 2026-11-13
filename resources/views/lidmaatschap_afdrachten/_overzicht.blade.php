{{--
    Overzicht per jaar van afdrachten op basis van het lidmaatschapspakket.
    Verwacht: $soort, $overzicht.
--}}
@php
    $euro = fn ($b) => '€ '.number_format($b, 2, ',', '.');
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
    <div class="px-5 py-3 border-b border-gray-100">
        <h2 class="font-semibold text-gray-800">Afdracht lidmaatschappen per jaar</h2>
        <p class="text-xs text-gray-500 mt-0.5">Op basis van de actieve leden en de afdracht per pakket.</p>
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
                <th class="px-5 py-3 text-left font-semibold text-gray-600">Jaar overgemaakt</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($overzicht as $rij)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-900">{{ $rij['jaar'] }}</td>
                <td class="px-5 py-3 text-right text-gray-600">{{ $rij['aantal'] }}</td>
                <td class="px-5 py-3 text-right whitespace-nowrap">{{ $euro($rij['totaal']) }}</td>
                <td class="px-5 py-3 text-right whitespace-nowrap text-green-600">{{ $euro($rij['overgemaakt']) }}</td>
                <td class="px-5 py-3 text-right whitespace-nowrap text-orange-500">{{ $euro($rij['open']) }}</td>
                <td class="px-5 py-3">
                    @if ($rij['aantal'] === 0)
                        <span class="text-xs text-gray-400">—</span>
                    @elseif ($rij['open_aantal'] === 0)
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                Ja, op {{ $rij['overgemaakt_op']->format('d-m-Y') }}
                            </span>
                            <form method="POST" action="{{ route('lidmaatschap-afdrachten.jaar-ongedaan', $soort) }}"
                                  onsubmit="return confirm('Markering overgemaakt voor {{ $rij['jaar'] }} verwijderen?')">
                                @csrf @method('DELETE')
                                <input type="hidden" name="jaar" value="{{ $rij['jaar'] }}">
                                <button type="submit" class="text-xs text-gray-400 hover:text-red-600">Ongedaan maken</button>
                            </form>
                        </div>
                    @else
                        <form method="POST" action="{{ route('lidmaatschap-afdrachten.markeer-alles', $soort) }}" class="flex flex-wrap items-center gap-2"
                              onsubmit="return confirm('{{ $rij['jaar'] }} markeren als overgemaakt?')">
                            @csrf
                            <input type="hidden" name="jaar" value="{{ $rij['jaar'] }}">
                            @if ($rij['overgemaakt'] > 0)
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Deels</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">Nee</span>
                            @endif
                            <input type="date" name="overgemaakt_op" value="{{ now()->format('Y-m-d') }}" required
                                   class="border border-gray-300 rounded-lg px-2 py-0.5 text-xs">
                            <button type="submit" class="text-xs text-bb-green-600 hover:underline font-medium">Markeer jaar overgemaakt</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
