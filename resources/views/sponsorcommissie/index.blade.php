@extends('layouts.app')
@section('title', 'Afdracht sponsorcommissie – BABB Portaal')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 mb-6">Afdracht sponsorcommissie</h1>

@include('lidmaatschap_afdrachten._overzicht', ['soort' => 'sponsorcommissie', 'indexRoute' => 'sponsorcommissie.index'])
@endsection
