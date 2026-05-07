@extends('layouts.app')
@section('title', 'Handleiding — BABB Portaal')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Handleiding</h1>
    <p class="text-gray-500 text-sm mt-1">Uitleg over het gebruik van het BABB Portaal.</p>
</div>

<div class="space-y-6 max-w-3xl">

    {{-- Dashboard --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-lg bg-bb-green-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-800">Dashboard</h2>
        </div>
        <p class="text-sm text-gray-600 mb-2">Het dashboard geeft een overzicht van de meest belangrijke informatie:</p>
        <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside ml-1">
            <li><strong>KPI-kaarten</strong> bovenaan: actieve leden, openstaande en verlopen facturen, omzet dit jaar.</li>
            <li><strong>Recente facturen</strong>: de laatste 5 facturen met status en bedrag.</li>
            <li><strong>Aankomende evenementen</strong>: evenementen die nog moeten plaatsvinden.</li>
            <li><strong>Verlopen lidmaatschappen</strong>: leden waarvan het lidmaatschap binnen 30 dagen verloopt.</li>
            <li><strong>Actieve leads</strong>: potentiele leden die nog opgevolgd moeten worden.</li>
        </ul>
    </div>

    {{-- Leden --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-lg bg-bb-green-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-800">Leden</h2>
        </div>
        <p class="text-sm text-gray-600 mb-2">Beheer alle leden van de business club.</p>
        <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside ml-1">
            <li>Zoek en filter op naam, e-mail, bedrijf, status of lidmaatschapstype.</li>
            <li>Sorteer de lijst op naam, bedrijf, e-mail, status of verloopdatum door op de kolomkop te klikken.</li>
            <li>Exporteer alle leden als CSV-bestand (te openen in Excel).</li>
            <li>Importeer leden via een CSV-bestand. Bestaande leden worden bijgewerkt op basis van e-mailadres.</li>
            <li>Stel per lid een lidmaatschapstype, start- en einddatum en status in (actief / inactief / geschorst).</li>
        </ul>
    </div>

    {{-- Leads --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-lg bg-bb-green-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-800">Leads (potentiele leden)</h2>
        </div>
        <p class="text-sm text-gray-600 mb-2">Leg potentiele leden vast en volg ze op tot lidmaatschap.</p>
        <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside ml-1">
            <li>Registreer wie de lead heeft aangedragen (bestaand lid of een naam).</li>
            <li>Wijs een opvolger toe (gebruiker binnen het portaal).</li>
            <li>Gebruik de statusflow: <strong>Nieuw</strong> → <strong>In contact</strong> → <strong>Follow-up</strong> → <strong>Gewonnen</strong> of <strong>Verloren</strong>.</li>
            <li>Zet een lead om naar lid via de knop <em>Omzetten naar lid</em>. De gegevens worden overgenomen.</li>
        </ul>
    </div>

    {{-- Evenementen --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-lg bg-bb-green-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-800">Evenementen</h2>
        </div>
        <p class="text-sm text-gray-600 mb-2">Plan en beheer evenementen van de business club.</p>
        <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside ml-1">
            <li>Maak evenementen aan met datum, locatie, beschrijving en status (concept / bevestigd).</li>
            <li>Voeg taken toe per evenement en markeer ze als open of gedaan.</li>
            <li>Aankomende evenementen verschijnen automatisch op het dashboard.</li>
        </ul>
    </div>

    {{-- Facturen --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-lg bg-bb-green-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-800">Facturen</h2>
        </div>
        <p class="text-sm text-gray-600 mb-2">Maak en beheer facturen voor leden.</p>
        <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside ml-1">
            <li>Maak een factuur aan via <em>Facturen → Nieuwe factuur</em>. Voeg regels toe met omschrijving, aantal, prijs en BTW.</li>
            <li>Een factuur begint als <strong>concept</strong>. Alleen een admin kan een factuur op <strong>verzonden</strong> zetten.</li>
            <li>Zodra betaald is, zet je de factuur op <strong>betaald</strong> via de knop op de detailpagina.</li>
            <li>Facturen met een verlopen betaaldatum worden automatisch als <strong>verlopen</strong> getoond.</li>
            <li>Download elke factuur als PDF via de knop op de detailpagina.</li>
        </ul>
    </div>

    {{-- Factureren --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-lg bg-bb-green-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-800">Factureren (lidmaatschapsfacturen)</h2>
        </div>
        <p class="text-sm text-gray-600 mb-2">Genereer in een keer facturen voor alle actieve leden.</p>
        <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside ml-1">
            <li>Ga naar <em>Factureren</em> en kies de periode en het bedrag.</li>
            <li>Het systeem maakt automatisch een conceptfactuur aan voor elk actief lid.</li>
            <li>Controleer de facturen daarna via <em>Facturen</em> en zet ze op verzonden als ze gereed zijn.</li>
        </ul>
    </div>

    {{-- Pakketten --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-lg bg-bb-green-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-800">Pakketten (lidmaatschapstypen)</h2>
        </div>
        <p class="text-sm text-gray-600 mb-2">Definieer de lidmaatschapsvormen van de club.</p>
        <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside ml-1">
            <li>Maak pakketten aan met naam, prijs, BTW-tarief en facturatiecyclus (maandelijks / jaarlijks).</li>
            <li>Koppel een pakket aan een lid via de ledenpagina.</li>
            <li>Inactieve pakketten zijn niet meer te selecteren bij nieuwe leden.</li>
        </ul>
    </div>

    @if (auth()->user()->isAdmin())
    {{-- Beheer --}}
    <div class="bg-white rounded-xl shadow-sm border border-bb-red-600 border-opacity-30 p-6">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-lg bg-bb-red-600 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-800">Beheer <span class="text-xs font-normal text-bb-red-600 ml-1">(alleen admin)</span></h2>
        </div>
        <p class="text-sm text-gray-600 mb-3">Het beheermenu is alleen zichtbaar voor administrators.</p>
        <div class="space-y-3">
            <div>
                <p class="text-sm font-medium text-gray-700">Gebruikers</p>
                <p class="text-sm text-gray-600">Maak portaalgebruikers aan en wijs rollen toe: <strong>Beheerder</strong> (alles), <strong>Bestuur</strong> (alles behalve facturen verzenden) of <strong>Gebruiker</strong> (alleen het dashboard met evenementen).</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Instellingen</p>
                <p class="text-sm text-gray-600">Upload een eigen logo dat links bovenin de navigatie verschijnt. Stel een achtergrondafbeelding in die achter het portaal wordt getoond.</p>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
