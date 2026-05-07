<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            'company_address'  => "Kolenbranderweg 11\n7482 SE Haaksbergen",
            'company_kvk'      => '40073489',
            'company_vat'      => 'NL0058.60.908.B.02',
        ];

        foreach ($defaults as $key => $value) {
            \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    public function down(): void
    {
        \App\Models\Setting::whereIn('key', ['company_address', 'company_kvk', 'company_vat'])->delete();
    }
};
