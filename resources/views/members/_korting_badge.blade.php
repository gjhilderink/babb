@if ($member->hasDiscount())
    @php
        $actief = $member->hasActiveDiscount();
        $status = $actief ? '' : ($member->korting_vanaf && $member->korting_vanaf->isFuture() ? ' (gepland)' : ' (verlopen)');
        $periode = ($member->korting_vanaf ? 'vanaf ' . $member->korting_vanaf->format('d-m-Y') . ' ' : '')
                 . ($member->korting_tot ? 't/m ' . $member->korting_tot->format('d-m-Y') : 'geen einddatum');
    @endphp
    <span class="block w-fit mt-0.5 px-2 py-0.5 rounded-full text-xs font-medium whitespace-nowrap
                 {{ $actief ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}"
          title="{{ $member->korting_reden }} &middot; {{ $periode }}">
        Korting {{ $member->korting_label }}{{ $status }}
    </span>
@endif
