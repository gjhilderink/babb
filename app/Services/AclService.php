<?php

namespace App\Services;

use App\Models\Setting;

class AclService
{
    /**
     * All managed permissions with their display labels.
     */
    public static function permissions(): array
    {
        return [
            'Facturen' => [
                'invoices.view'   => 'Facturen inzien',
                'invoices.create' => 'Facturen aanmaken',
                'invoices.edit'   => 'Facturen bewerken / markeren',
                'invoices.delete' => 'Facturen verwijderen',
            ],
            'Leden' => [
                'members.view'   => 'Leden inzien',
                'members.manage' => 'Leden aanmaken / bewerken / verwijderen',
            ],
            'Leads' => [
                'leads.view'   => 'Leads inzien',
                'leads.manage' => 'Leads aanmaken / bewerken / verwijderen / converteren',
            ],
            'Evenementen' => [
                'events.view'   => 'Evenementen inzien',
                'events.manage' => 'Evenementen aanmaken / bewerken / verwijderen',
            ],
            'Vergaderingen' => [
                'meetings.view'   => 'Vergaderingen inzien',
                'meetings.manage' => 'Vergaderingen aanmaken / bewerken / verwijderen',
            ],
            'Overig' => [
                'membership_billing'      => 'Contributies factureren',
                'membership_types.manage' => 'Pakketten beheren',
                'products.manage'         => 'Producten beheren',
            ],
        ];
    }

    /**
     * All permission keys flattened.
     */
    public static function allKeys(): array
    {
        return array_merge(...array_values(array_map('array_keys', static::permissions())));
    }

    /**
     * Default ACL for bestuur: everything allowed except invoice create/edit/delete.
     */
    public static function defaults(): array
    {
        $bestuur   = array_fill_keys(static::allKeys(), true);
        $gebruiker = array_fill_keys(static::allKeys(), false);

        $bestuur['invoices.create'] = false;
        $bestuur['invoices.edit']   = false;
        $bestuur['invoices.delete'] = false;

        return [
            'bestuur'   => $bestuur,
            'gebruiker' => $gebruiker,
        ];
    }

    /**
     * Check whether the current user has a given permission.
     * Admin always passes. Returns false if not authenticated.
     */
    public static function allowed(string $permission): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        if ($user->isAdmin()) return true;

        $acl = json_decode(Setting::get('acl', '{}'), true) ?? [];
        return (bool) ($acl[$user->role][$permission] ?? false);
    }
}
