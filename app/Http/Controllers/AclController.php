<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\AclService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AclController extends Controller
{
    public function edit(): View
    {
        $acl         = json_decode(Setting::get('acl', '{}'), true) ?? [];
        $permissions = AclService::permissions();
        $roles       = ['bestuur', 'gebruiker'];

        return view('acl.edit', compact('acl', 'permissions', 'roles'));
    }

    public function update(Request $request): RedirectResponse
    {
        $allKeys = AclService::allKeys();
        $roles   = ['bestuur', 'gebruiker'];

        $aclInput = $request->input('acl', []);

        $acl = [];
        foreach ($roles as $role) {
            foreach ($allKeys as $key) {
                $acl[$role][$key] = isset($aclInput[$role][$key]);
            }
        }

        Setting::set('acl', json_encode($acl));

        return back()->with('success', 'Toegangsrechten opgeslagen.');
    }
}
