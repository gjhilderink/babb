@extends('layouts.app')
@section('title', 'Gebruikers — BABB Portaal')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Gebruikers</h1>
    <a href="{{ route('users.create') }}"
       class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        + Nieuwe gebruiker
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Naam</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">E-mailadres</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Rol</th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600">Aangemaakt</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($users as $user)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-900">
                    {{ $user->name }}
                    @if ($user->id === auth()->id())
                        <span class="ml-1 text-xs text-gray-400">(jij)</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $user->isAdmin() ? 'bg-bb-red-600 text-white' : '' }}
                        {{ $user->isBestuur() ? 'bg-bb-green-600 text-white' : '' }}
                        {{ $user->isGebruiker() ? 'bg-gray-200 text-gray-700' : '' }}">
                        {{ $user->roleName() }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $user->created_at->format('d-m-Y') }}</td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('users.edit', $user) }}"
                           class="text-xs text-bb-green-600 hover:underline font-medium">Bewerken</a>
                        <form method="POST" action="{{ route('users.send-welcome', $user) }}"
                              onsubmit="return confirm('Welkomstmail sturen naar {{ $user->email }}? Het wachtwoord wordt opnieuw ingesteld.')">
                            @csrf
                            <button class="text-xs text-blue-600 hover:underline font-medium">Welkomstmail</button>
                        </form>
                        @if (!$user->isAdmin() && !session('impersonator_id'))
                        <form method="POST" action="{{ route('users.impersonate', $user) }}"
                              onsubmit="return confirm('Inloggen als {{ $user->name }}?')">
                            @csrf
                            <button class="text-xs text-purple-600 hover:underline font-medium">Login als</button>
                        </form>
                        @endif
                        @if ($user->id !== auth()->id())
                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Gebruiker verwijderen?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-bb-red-600 hover:underline font-medium">Verwijderen</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-8 text-center text-gray-400">Geen gebruikers gevonden.</td>
            </tr>
            @endforelse
        </tbody>
    </table></div></div>
@endsection

