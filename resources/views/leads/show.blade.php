@extends('layouts.app')
@section('title', $lead->full_name . ' — BABB Portaal')

@section('content')
<div class="flex flex-wrap justify-between items-start gap-3 mb-6">
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('leads.index') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Potentiele leden</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $lead->full_name }}</h1>
        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $lead->statusColor() }}">
            {{ $lead->statusLabel() }}
        </span>
    </div>
    <div class="flex flex-wrap gap-2">
        @if (!$lead->isConverted())
        <a href="{{ route('leads.convert-form', $lead) }}"
           class="bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            Omzetten naar lid
        </a>
        @else
        <a href="{{ route('members.show', $lead->member) }}"
           class="border border-bb-green-600 text-bb-green-700 hover:bg-bb-green-50 text-sm font-medium px-4 py-2 rounded-lg">
            Bekijk lid
        </a>
        @endif
        <a href="{{ route('leads.edit', $lead) }}"
           class="border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg">
            Bewerken
        </a>
    </div>
</div>

@if ($lead->isConverted())
<div class="mb-6 bg-green-50 border border-green-200 rounded-xl px-5 py-4 text-sm text-green-800 flex items-center gap-3">
    <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>
        Omgezet naar lid op <strong>{{ $lead->converted_at->format('d-m-Y') }}</strong>.
        <a href="{{ route('members.show', $lead->member) }}" class="underline ml-1">Bekijk het lidprofiel</a>
    </span>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Gegevens</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div><dt class="text-gray-500">E-mail</dt><dd class="font-medium mt-0.5">{{ $lead->email ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Telefoon</dt><dd class="font-medium mt-0.5">{{ $lead->phone ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Bedrijf</dt><dd class="font-medium mt-0.5">{{ $lead->company_name ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Kanaal / bron</dt><dd class="font-medium mt-0.5">{{ $lead->source ?? '—' }}</dd></div>
            </dl>

            @if ($lead->notes)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <dt class="text-gray-500 text-sm mb-1">Notities</dt>
                <dd class="text-sm text-gray-700 whitespace-pre-line">{{ $lead->notes }}</dd>
            </div>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-800 mb-4">Herkomst &amp; opvolging</h2>
            <dl class="space-y-4 text-sm">
                <div>
                    <dt class="text-gray-500 mb-0.5">Aangemeld door</dt>
                    @if ($lead->referredByMember)
                        <dd class="font-medium">
                            <a href="{{ route('members.show', $lead->referredByMember) }}" class="text-bb-green-700 hover:underline">
                                {{ $lead->referredByMember->full_name }}
                            </a>
                            <span class="text-gray-400 text-xs ml-1">(lid)</span>
                        </dd>
                    @elseif ($lead->referred_by_name)
                        <dd class="font-medium">{{ $lead->referred_by_name }}</dd>
                    @else
                        <dd class="text-gray-400">—</dd>
                    @endif
                </div>
                <div>
                    <dt class="text-gray-500 mb-0.5">Opvolging door</dt>
                    @if ($lead->assignedTo)
                        <dd class="font-medium">{{ $lead->assignedTo->name }}</dd>
                    @else
                        <dd class="text-gray-400">Niet toegewezen</dd>
                    @endif
                </div>
                <div>
                    <dt class="text-gray-500 mb-0.5">Aangemeld op</dt>
                    <dd class="font-medium">{{ $lead->created_at->format('d-m-Y') }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-800 mb-3">Status wijzigen</h2>
            <form method="POST" action="{{ route('leads.update', $lead) }}">
                @csrf @method('PUT')
                <input type="hidden" name="first_name" value="{{ $lead->first_name }}">
                <input type="hidden" name="last_name"  value="{{ $lead->last_name }}">
                <input type="hidden" name="email"      value="{{ $lead->email }}">
                <input type="hidden" name="phone"      value="{{ $lead->phone }}">
                <input type="hidden" name="company_name" value="{{ $lead->company_name }}">
                <input type="hidden" name="source"     value="{{ $lead->source }}">
                <input type="hidden" name="notes"      value="{{ $lead->notes }}">
                <input type="hidden" name="referred_by_member_id" value="{{ $lead->referred_by_member_id }}">
                <input type="hidden" name="referred_by_name"      value="{{ $lead->referred_by_name }}">
                <input type="hidden" name="assigned_to_user_id"   value="{{ $lead->assigned_to_user_id }}">
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-3">
                    @foreach (['nieuw'=>'Nieuw','contact'=>'In contact','follow_up'=>'Follow-up nodig','gewonnen'=>'Gewonnen','verloren'=>'Verloren'] as $val => $label)
                        <option value="{{ $val }}" @selected($lead->status === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full bg-bb-green-600 hover:bg-bb-green-700 text-white text-sm font-medium py-2 rounded-lg">
                    Status opslaan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
