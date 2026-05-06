<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen — BABB Portaal</title>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'bb-green': { 600: '#5ea31f', 700: '#4d8a18' },
                        'bb-red':   { 600: '#cc1c1c' },
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex flex-col items-center justify-center">

    {{-- Accent stripes --}}
    <div class="fixed top-0 left-0 w-full h-1 flex">
        <div class="w-1/2 bg-bb-red-600"></div>
        <div class="w-1/2 bg-bb-green-600"></div>
    </div>

    <div class="w-full max-w-md px-4">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-3 mb-2">
                <span class="text-4xl font-black text-white tracking-tight">BABB</span>
                <div class="flex flex-col gap-0.5">
                    <div class="h-2 w-12 bg-bb-red-600 rounded-sm"></div>
                    <div class="h-2 w-12 bg-bb-green-600 rounded-sm"></div>
                </div>
            </div>
            <p class="text-gray-400 text-sm">Business club administratie portaal</p>
        </div>

        <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
            {{-- Header stripe --}}
            <div class="h-1 flex">
                <div class="w-1/2 bg-bb-red-600"></div>
                <div class="w-1/2 bg-bb-green-600"></div>
            </div>

            <div class="p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Inloggen</h2>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mailadres</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full border @error('email') border-red-400 @else border-gray-300 @enderror rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        @error('email')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Wachtwoord</label>
                        <input id="password" type="password" name="password" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div class="flex items-center mb-6">
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-gray-300">
                            Onthoud mij
                        </label>
                    </div>

                    <button type="submit"
                            class="w-full bg-bb-green-600 hover:bg-bb-green-700 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
                        Inloggen
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center text-gray-600 text-xs mt-6">#RoodGroen Verbindt</p>
    </div>
</body>
</html>
