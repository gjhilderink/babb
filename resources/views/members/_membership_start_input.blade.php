<form method="POST" action="{{ route('members.membership-start', $member) }}" class="inline">
    @csrf @method('PATCH')
    <input type="date" name="membership_start" title="Lid sinds wijzigen"
           value="{{ $member->membership_start?->format('Y-m-d') }}"
           @if ($member->membership_end) max="{{ $member->membership_end->format('Y-m-d') }}" @endif
           onchange="this.form.submit()"
           class="text-sm text-gray-700 bg-transparent border border-transparent hover:border-gray-300 rounded-lg px-1 py-0.5 cursor-pointer focus:outline-none focus:ring-2 focus:ring-bb-green-600">
</form>
