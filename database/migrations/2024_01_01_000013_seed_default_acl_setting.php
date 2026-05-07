<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $existing = DB::table('settings')->where('key', 'acl')->first();
        if ($existing) return;

        $keys = [
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete',
            'members.view', 'members.manage',
            'leads.view', 'leads.manage',
            'events.view', 'events.manage',
            'membership_billing', 'membership_types.manage', 'products.manage',
        ];

        $bestuur   = array_fill_keys($keys, true);
        $gebruiker = array_fill_keys($keys, false);

        $bestuur['invoices.create'] = false;
        $bestuur['invoices.edit']   = false;
        $bestuur['invoices.delete'] = false;

        DB::table('settings')->insert([
            'key'        => 'acl',
            'value'      => json_encode(['bestuur' => $bestuur, 'gebruiker' => $gebruiker]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'acl')->delete();
    }
};
