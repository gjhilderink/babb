@extends('layouts.app')
@section('title', 'Nieuw evenement — BABB Portaal')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('events.index') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Evenementen</a>
    <h1 class="text-2xl font-bold text-gray-900">Nieuw evenement</h1>
</div>

<form method="POST" action="{{ route('events.store') }}">
    @csrf
    @include('events._form')
    <div class="mt-6 flex gap-3">
        <button type="submit" class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
            Evenement aanmaken
        </button>
        <a href="{{ route('events.index') }}" class="text-sm text-gray-600 hover:text-gray-800 px-4 py-2">Annuleren</a>
    </div>
</form>
@endsection
