@extends('layouts.app')
@section('title', 'Nieuw pakket — BABB Portaal')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('membership-types.index') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Pakketten</a>
    <h1 class="text-2xl font-bold text-gray-900">Nieuw pakket</h1>
</div>

<form method="POST" action="{{ route('membership-types.store') }}"
      class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-2xl">
    @csrf
    @include('membership_types._form')
    <div class="mt-6 flex gap-3">
        <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
            Pakket aanmaken
        </button>
        <a href="{{ route('membership-types.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2">Annuleren</a>
    </div>
</form>
@endsection
