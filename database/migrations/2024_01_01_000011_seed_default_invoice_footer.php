<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        \App\Models\Setting::updateOrCreate(
            ['key' => 'invoice_footer'],
            ['value' => "Gelieve uw betaling binnen 14 dagen over te maken naar onze rekening bij de\nRabobank te Haaksbergen rekening nummer NL05 RABO 0312 0744 76"]
        );
    }

    public function down(): void
    {
        \App\Models\Setting::where('key', 'invoice_footer')->delete();
    }
};
