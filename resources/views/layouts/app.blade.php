@php
    $siteLogo = \App\Models\Setting::get('logo');
    $siteBg   = \App\Models\Setting::get('background');
@endphp
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BABB Portaal')</title>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'bb-green': { 400:'#7bc43a', 500:'#6ab52a', 600:'#5ea31f', 700:'#4d8a18', 800:'#3d6e13' },
                        'bb-red':   { 500:'#e02020', 600:'#cc1c1c', 700:'#b01818' },
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .bg-bb-green-50  { background-color: #f0f8e8; }
        .bg-bb-green-100 { background-color: #dff0c4; }
        .bg-bb-green-400 { background-color: #7bc43a; }
        .bg-bb-green-500 { background-color: #6ab52a; }
        .bg-bb-green-600 { background-color: #5ea31f; }
        .bg-bb-green-700 { background-color: #4d8a18; }
        .bg-bb-green-800 { background-color: #3d6e13; }
        .hover\:bg-bb-green-50:hover  { background-color: #f0f8e8; }
        .hover\:bg-bb-green-600:hover { background-color: #5ea31f; }
        .hover\:bg-bb-green-700:hover { background-color: #4d8a18; }
        .hover\:bg-bb-green-800:hover { background-color: #3d6e13; }
        .text-bb-green-500 { color: #6ab52a; }
        .text-bb-green-600 { color: #5ea31f; }
        .text-bb-green-700 { color: #4d8a18; }
        .text-bb-green-800 { color: #3d6e13; }
        .border-bb-green-100 { border-color: #dff0c4; }
        .border-bb-green-500 { border-color: #6ab52a; }
        .border-bb-green-600 { border-color: #5ea31f; }
        .focus\:ring-bb-green-600:focus { --tw-ring-color: #5ea31f; box-shadow: 0 0 0 3px rgba(94,163,31,.35); }
        .bg-bb-red-500  { background-color: #e02020; }
        .bg-bb-red-600  { background-color: #cc1c1c; }
        .bg-bb-red-700  { background-color: #b01818; }
        .hover\:bg-bb-red-700:hover { background-color: #b01818; }
        .text-bb-red-500 { color: #e02020; }
        .text-bb-red-600 { color: #cc1c1c; }
        .text-bb-red-700 { color: #b01818; }
        .border-bb-red-600 { border-color: #cc1c1c; }
        .file\:bg-bb-red-600::file-selector-button  { background-color: #cc1c1c; color: #fff; }
        .hover\:file\:bg-bb-red-700:hover::file-selector-button { background-color: #b01818; }
        .file\:bg-bb-green-600::file-selector-button { background-color: #5ea31f; color: #fff; }
        .hover\:file\:bg-bb-green-700:hover::file-selector-button { background-color: #4d8a18; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen"@if($siteBg) style="background-image: url('{{ asset($siteBg) }}'); background-size: cover; background-attachment: fixed; background-position: center;"@endif>
@if($siteBg)<div class="fixed inset-0 bg-gray-900 bg-opacity-60 -z-10"></div>@endif

<div class="h-1 w-full flex">
    <div class="w-1/2 bg-bb-red-600"></div>
    <div class="w-1/2 bg-bb-green-600"></div>
</div>

<nav class="bg-gray-900 shadow-lg" x-data="{ open: false, admin: false, billing: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            {{-- Logo --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 shrink-0">
                @if($siteLogo)
                    <img src="{{ asset($siteLogo) }}" alt="Logo" class="h-9 w-auto max-w-[160px] object-contain">
                @else
                    <span class="text-white font-bold text-lg tracking-wide"><span class="text-bb-green-500">BABB</span> <span class="text-gray-400 font-normal text-sm">Portaal</span></span>
                @endif
            </a>

            {{-- Desktop nav --}}
            <div class="hidden md:flex gap-1">
                <a href="{{ route('dashboard') }}"
                   class="px-3 py-2 rounded text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-bb-green-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Dashboard
                </a>
                @if (auth()->user()->isAdminOrBestuur())
                <a href="{{ route('membership-types.index') }}"
                   class="px-3 py-2 rounded text-sm font-medium transition-colors {{ request()->routeIs('membership-types*') ? 'bg-bb-green-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Pakketten
                </a>
                <a href="{{ route('members.index') }}"
                   class="px-3 py-2 rounded text-sm font-medium transition-colors {{ request()->routeIs('members.*') ? 'bg-bb-green-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Leden
                </a>
                <a href="{{ route('leads.index') }}"
                   class="px-3 py-2 rounded text-sm font-medium transition-colors {{ request()->routeIs('leads*') ? 'bg-bb-green-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Leads
                </a>
                <a href="{{ route('events.index') }}"
                   class="px-3 py-2 rounded text-sm font-medium transition-colors {{ request()->routeIs('events*') ? 'bg-bb-green-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Evenementen
                </a>
                @if(\App\Services\AclService::allowed('meetings.view'))
                <a href="{{ route('meetings.index') }}"
                   class="px-3 py-2 rounded text-sm font-medium transition-colors {{ request()->routeIs('meetings*') ? 'bg-bb-green-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Vergaderingen
                </a>
                @endif
                <a href="{{ route('tasks.index') }}"
                   class="px-3 py-2 rounded text-sm font-medium transition-colors {{ request()->routeIs('tasks*') ? 'bg-bb-green-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                    Taken
                </a>
                @if(\App\Services\AclService::allowed('membership_billing') || \App\Services\AclService::allowed('invoices.view'))
                <div class="relative" x-data @click.outside="billing=false">
                    <button @click="billing=!billing"
                            class="px-3 py-2 rounded text-sm font-medium transition-colors flex items-center gap-1
                                {{ request()->routeIs('membership-billing*','invoices*') ? 'bg-bb-green-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                        Factureren
                        <svg class="w-3.5 h-3.5 transition-transform" :class="billing ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="billing" x-transition
                         class="absolute left-0 mt-1 w-44 bg-gray-800 rounded-lg shadow-lg border border-gray-700 py-1 z-50">
                        @if(\App\Services\AclService::allowed('membership_billing'))
                        <a href="{{ route('membership-billing.index') }}" @click="billing=false"
                           class="block px-4 py-2 text-sm {{ request()->routeIs('membership-billing*') ? 'text-white bg-bb-green-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                            Contributies factureren
                        </a>
                        @endif
                        @if(\App\Services\AclService::allowed('invoices.view'))
                        <a href="{{ route('invoices.index') }}" @click="billing=false"
                           class="block px-4 py-2 text-sm {{ request()->routeIs('invoices*') ? 'text-white bg-bb-green-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                            Facturen
                        </a>
                        @endif
                    </div>
                </div>
                @endif
                @endif
                @if (auth()->user()->isAdmin())
                {{-- Beheer dropdown --}}
                <div class="relative" x-data @click.outside="admin=false">
                    <button @click="admin=!admin"
                            class="px-3 py-2 rounded text-sm font-medium transition-colors flex items-center gap-1
                                {{ request()->routeIs('users*','settings*') ? 'bg-bb-red-700 text-white' : 'text-gray-300 hover:text-white hover:bg-bb-red-700' }}">
                        Beheer
                        <svg class="w-3.5 h-3.5 transition-transform" :class="admin ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="admin" x-transition
                         class="absolute right-0 mt-1 w-44 bg-gray-800 rounded-lg shadow-lg border border-gray-700 py-1 z-50">
                        <a href="{{ route('users.index') }}" @click="admin=false"
                           class="flex items-center gap-2 px-4 py-2 text-sm {{ request()->routeIs('users*') ? 'text-white bg-bb-red-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
                            Gebruikers
                        </a>
                        <a href="{{ route('settings.edit') }}" @click="admin=false"
                           class="flex items-center gap-2 px-4 py-2 text-sm {{ request()->routeIs('settings*') ? 'text-white bg-bb-red-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Instellingen
                        </a>
                        <a href="{{ route('acl.edit') }}" @click="admin=false"
                           class="flex items-center gap-2 px-4 py-2 text-sm {{ request()->routeIs('acl*') ? 'text-white bg-bb-red-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Toegangsrechten
                        </a>
                        <div class="border-t border-gray-700 my-1"></div>
                        <a href="{{ route('handleiding') }}" @click="admin=false"
                           class="flex items-center gap-2 px-4 py-2 text-sm {{ request()->routeIs('handleiding') ? 'text-white bg-bb-red-700' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            Handleiding
                        </a>
                    </div>
                </div>
                @endif
            </div>

            {{-- Desktop user + logout --}}
            <div class="hidden md:flex items-center gap-3">
                <span class="text-xs text-gray-400">
                    {{ auth()->user()->name }}
                    <span class="ml-1 px-1.5 py-0.5 rounded text-xs
                        {{ auth()->user()->isAdmin() ? 'bg-bb-red-700 text-white' : '' }}
                        {{ auth()->user()->isBestuur() ? 'bg-bb-green-700 text-white' : '' }}
                        {{ auth()->user()->isGebruiker() ? 'bg-gray-600 text-gray-300' : '' }}">
                        {{ auth()->user()->roleName() }}
                    </span>
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-sm text-gray-400 hover:text-white border border-gray-600 hover:border-gray-400 rounded px-3 py-1.5 transition-colors">
                        Uitloggen
                    </button>
                </form>
            </div>

            {{-- Mobile hamburger --}}
            <button @click="open = !open" class="md:hidden text-gray-400 hover:text-white p-2 rounded">
                <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="open" x-transition class="md:hidden border-t border-gray-700 bg-gray-900 pb-3">
        <div class="px-4 pt-3 space-y-1">
            <a href="{{ route('dashboard') }}" @click="open=false"
               class="block px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-bb-green-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                Dashboard
            </a>
            @if (auth()->user()->isAdminOrBestuur())
            <a href="{{ route('membership-types.index') }}" @click="open=false"
               class="block px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('membership-types*') ? 'bg-bb-green-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                Pakketten
            </a>
            <a href="{{ route('members.index') }}" @click="open=false"
               class="block px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('members.*') ? 'bg-bb-green-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                Leden
            </a>
            <a href="{{ route('leads.index') }}" @click="open=false"
               class="block px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('leads*') ? 'bg-bb-green-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                Leads
            </a>
            <a href="{{ route('events.index') }}" @click="open=false"
               class="block px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('events*') ? 'bg-bb-green-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                Evenementen
            </a>
            @if(\App\Services\AclService::allowed('meetings.view'))
            <a href="{{ route('meetings.index') }}" @click="open=false"
               class="block px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('meetings*') ? 'bg-bb-green-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                Vergaderingen
            </a>
            @endif
            <a href="{{ route('tasks.index') }}" @click="open=false"
               class="block px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('tasks*') ? 'bg-bb-green-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                Taken
            </a>
            @if(\App\Services\AclService::allowed('membership_billing') || \App\Services\AclService::allowed('invoices.view'))
            <p class="px-3 pt-2 pb-0.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Factureren</p>
            @if(\App\Services\AclService::allowed('membership_billing'))
            <a href="{{ route('membership-billing.index') }}" @click="open=false"
               class="block px-4 py-2 rounded text-sm font-medium {{ request()->routeIs('membership-billing*') ? 'bg-bb-green-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                Contributies factureren
            </a>
            @endif
            @if(\App\Services\AclService::allowed('invoices.view'))
            <a href="{{ route('invoices.index') }}" @click="open=false"
               class="block px-4 py-2 rounded text-sm font-medium {{ request()->routeIs('invoices*') ? 'bg-bb-green-700 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                Facturen
            </a>
            @endif
            @endif
            @endif
            @if (auth()->user()->isAdmin())
            <div class="pt-1 pb-0.5">
                <p class="px-3 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wide">Beheer</p>
                <a href="{{ route('users.index') }}" @click="open=false"
                   class="block px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('users*') ? 'bg-bb-red-700 text-white' : 'text-gray-300 hover:text-white hover:bg-bb-red-700' }}">
                    Gebruikers
                </a>
                <a href="{{ route('settings.edit') }}" @click="open=false"
                   class="block px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('settings*') ? 'bg-bb-red-700 text-white' : 'text-gray-300 hover:text-white hover:bg-bb-red-700' }}">
                    Instellingen
                </a>
                <a href="{{ route('acl.edit') }}" @click="open=false"
                   class="block px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('acl*') ? 'bg-bb-red-700 text-white' : 'text-gray-300 hover:text-white hover:bg-bb-red-700' }}">
                    Toegangsrechten
                </a>
                <a href="{{ route('handleiding') }}" @click="open=false"
                   class="block px-3 py-2 rounded text-sm font-medium {{ request()->routeIs('handleiding') ? 'bg-bb-red-700 text-white' : 'text-gray-300 hover:text-white hover:bg-bb-red-700' }}">
                    Handleiding
                </a>
            </div>
            @endif
        </div>
        <div class="mt-3 px-4 pt-3 border-t border-gray-700 flex items-center justify-between">
            <span class="text-xs text-gray-400">
                {{ auth()->user()->name }}
                <span class="ml-1 px-1.5 py-0.5 rounded text-xs
                    {{ auth()->user()->isAdmin() ? 'bg-bb-red-700 text-white' : '' }}
                    {{ auth()->user()->isBestuur() ? 'bg-bb-green-700 text-white' : '' }}
                    {{ auth()->user()->isGebruiker() ? 'bg-gray-600 text-gray-300' : '' }}">
                    {{ auth()->user()->roleName() }}
                </span>
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-sm text-gray-400 hover:text-white border border-gray-600 rounded px-3 py-1.5">
                    Uitloggen
                </button>
            </form>
        </div>
    </div>
</nav>

@if(session('impersonator_id'))
<div class="bg-yellow-400 text-yellow-900 text-sm font-medium px-4 py-2 flex items-center justify-between">
    <span>
        <svg class="inline w-4 h-4 mr-1 -mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        Je bent ingelogd als <strong>{{ auth()->user()->name }}</strong>
    </span>
    <form method="POST" action="{{ route('impersonate.stop') }}">
        @csrf
        <button class="ml-4 bg-yellow-900 text-yellow-100 hover:bg-yellow-800 text-xs font-semibold px-3 py-1 rounded">
            Terug naar eigen account
        </button>
    </form>
</div>
@endif

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    @if (session('success'))
        <div class="mb-4 rounded-md bg-bb-green-600 px-4 py-3 text-sm text-white flex items-center gap-2">
            <span>&#10003;</span> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif
    @yield('content')
</main>

</body>
</html>

