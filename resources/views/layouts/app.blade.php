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
                        'bb-green': {
                            400: '#7bc43a',
                            500: '#6ab52a',
                            600: '#5ea31f',
                            700: '#4d8a18',
                            800: '#3d6e13',
                        },
                        'bb-red': {
                            500: '#e02020',
                            600: '#cc1c1c',
                            700: '#b01818',
                        },
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">

{{-- Top accent bar --}}
<div class="h-1 w-full flex">
    <div class="w-1/2 bg-bb-red-600"></div>
    <div class="w-1/2 bg-bb-green-600"></div>
</div>

<nav class="bg-gray-900 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-8">
                <a href="{{ route('dashboard') }}" class="text-white font-bold text-lg tracking-wide flex items-center gap-2">
                    <span class="text-bb-green-500">BABB</span>
                    <span class="text-gray-400 font-normal text-sm">Portaal</span>
                </a>
                <div class="flex gap-1">
                    @foreach ([
                        'dashboard'              => ['route' => 'dashboard',              'label' => 'Dashboard'],
                        'membership-types'       => ['route' => 'membership-types.index', 'label' => 'Pakketten'],
                        'members'                => ['route' => 'members.index',          'label' => 'Leden'],
                        'events'                 => ['route' => 'events.index',           'label' => 'Evenementen'],
                        'invoices'               => ['route' => 'invoices.index',         'label' => 'Facturen'],
                        'membership-billing'     => ['route' => 'membership-billing.index','label' => 'Factureren'],
                    ] as $key => $item)
                    <a href="{{ route($item['route']) }}"
                       class="px-3 py-2 rounded text-sm font-medium transition-colors
                              {{ request()->routeIs($key.'*') || request()->routeIs($item['route'])
                                 ? 'bg-bb-green-700 text-white'
                                 : 'text-gray-300 hover:text-white hover:bg-gray-700' }}">
                        {{ $item['label'] }}
                    </a>
                    @endforeach
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-400">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="text-sm text-gray-400 hover:text-white border border-gray-600 hover:border-gray-400 rounded px-3 py-1.5 transition-colors">
                        Uitloggen
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if (session('success'))
        <div class="mb-4 rounded-md bg-bb-green-600 bg-opacity-10 border border-bb-green-600 border-opacity-30 px-4 py-3 text-sm text-bb-green-800 flex items-center gap-2">
            <span class="text-bb-green-600">&#10003;</span> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

</body>
</html>
