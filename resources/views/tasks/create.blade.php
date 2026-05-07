@extends('layouts.app')
@section('title', 'Nieuwe taak – BABB Portaal')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('tasks.index') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Taken</a>
    <h1 class="text-2xl font-bold text-gray-900">Nieuwe taak</h1>
</div>

<form method="POST" action="{{ route('tasks.store') }}">
    @csrf
    @include('tasks._form')
    <div class="mt-6 flex items-center gap-3">
        <button type="submit"
                class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
            Opslaan
        </button>
        <a href="{{ route('tasks.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Annuleren</a>
    </div>
</form>
@endsection
