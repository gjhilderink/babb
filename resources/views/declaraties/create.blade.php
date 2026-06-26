@extends('layouts.app')
@section('title', 'Nieuwe declaratie – BABB Portaal')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('declaraties.index') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Declaraties</a>
    <h1 class="text-2xl font-bold text-gray-900">Nieuwe declaratie</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-xl">
    <form method="POST" action="{{ route('declaraties.store') }}" enctype="multipart/form-data">
        @csrf
        @include('declaraties._form')
        <div class="mt-6 flex gap-3">
            <button type="submit"
                    class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                Opslaan
            </button>
            <a href="{{ route('declaraties.index') }}"
               class="text-sm text-gray-500 hover:text-gray-800 px-4 py-2">Annuleren</a>
        </div>
    </form>
</div>
@endsection
