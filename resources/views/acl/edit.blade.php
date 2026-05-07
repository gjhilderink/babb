@extends('layouts.app')
@section('title', 'Toegangsrechten — BABB Portaal')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Toegangsrechten</h1>
    <p class="text-sm text-gray-500">Beheerders hebben altijd volledige toegang. Onderstaande instellingen gelden voor Bestuur en Gebruiker.</p>
</div>

<form method="POST" action="{{ route('acl.update') }}">
    @csrf @method('PUT')

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 w-full">Functie</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 whitespace-nowrap">Bestuur</th>
                        <th class="px-6 py-3 text-center font-semibold text-gray-600 whitespace-nowrap">Gebruiker</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($permissions as $group => $items)
                    <tr class="bg-gray-50">
                        <td colspan="3" class="px-6 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $group }}</td>
                    </tr>
                    @foreach ($items as $key => $label)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-gray-700">{{ $label }}</td>
                        @foreach ($roles as $role)
                        <td class="px-6 py-3 text-center">
                            <input type="checkbox"
                                   name="acl[{{ $role }}][{{ $key }}]"
                                   value="1"
                                   {{ ($acl[$role][$key] ?? false) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-bb-green-600 focus:ring-bb-green-600 cursor-pointer">
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit"
                class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
            Opslaan
        </button>
    </div>
</form>
@endsection
