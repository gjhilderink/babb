@extends('layouts.app')
@section('title', 'Gebruiker bewerken — BABB Portaal')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Gebruikers</a>
    <h1 class="text-2xl font-bold text-gray-900">Gebruiker bewerken</h1>
</div>

<form method="POST" action="{{ route('users.update', $user) }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-lg">
    @csrf @method('PUT')
    <div class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Naam <span class="text-bb-red-600">*</span></label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">E-mailadres <span class="text-bb-red-600">*</span></label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nieuw wachtwoord <span class="text-gray-400 font-normal">(leeg laten = niet wijzigen)</span></label>
            <input type="password" name="password" minlength="8"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Wachtwoord bevestigen</label>
            <input type="password" name="password_confirmation"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Rol <span class="text-bb-red-600">*</span></label>
            <select name="role" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-bb-green-600">
                <option value="gebruiker" @selected(old('role', $user->role) === 'gebruiker')>Gebruiker — alleen evenementen op dashboard</option>
                <option value="bestuur" @selected(old('role', $user->role) === 'bestuur')>Bestuur — alles behalve facturen sturen</option>
                <option value="admin" @selected(old('role', $user->role) === 'admin')>Beheerder — volledige toegang</option>
            </select>
        </div>
    </div>
    <div class="mt-6 flex gap-3">
        <button type="submit" class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
            Opslaan
        </button>
        <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2">Annuleren</a>
    </div>
</form>
@endsection
