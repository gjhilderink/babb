<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function start(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 403, 'Kan niet inloggen als een andere beheerder.');
        abort_if(session()->has('impersonator_id'), 403, 'Al aan het impersoneren.');

        session(['impersonator_id' => auth()->id()]);
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', "Je bent nu ingelogd als {$user->name}.");
    }

    public function stop(): RedirectResponse
    {
        $adminId = session()->pull('impersonator_id');

        if (!$adminId) {
            return redirect()->route('dashboard');
        }

        $admin = User::findOrFail($adminId);
        Auth::login($admin);

        return redirect()->route('users.index')->with('success', 'Teruggekeerd naar je eigen account.');
    }
}
