<form method="POST" action="{{ route('members.membership-type', $member) }}" class="inline">
    @csrf @method('PATCH')
    <select name="membership_type_id" onchange="this.form.submit()" title="Abonnement wijzigen"
            class="text-sm text-gray-700 bg-transparent border border-transparent hover:border-gray-300 rounded-lg px-1 py-0.5 cursor-pointer focus:outline-none focus:ring-2 focus:ring-bb-green-600 max-w-full">
        <option value="" @selected(! $member->membership_type_id)>— Geen —</option>
        @foreach ($membershipTypes as $type)
            <option value="{{ $type->id }}" @selected($member->membership_type_id == $type->id)>{{ $type->name }}</option>
        @endforeach
        @if ($member->membershipType && ! $membershipTypes->contains('id', $member->membership_type_id))
            <option value="{{ $member->membership_type_id }}" selected>{{ $member->membershipType->name }} (inactief)</option>
        @endif
    </select>
</form>
