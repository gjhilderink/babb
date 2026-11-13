<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_types', function (Blueprint $table) {
            $table->decimal('afdracht_sponsorcommissie', 10, 2)->default(0)->after('price_per_year');
        });
    }

    public function down(): void
    {
        Schema::table('membership_types', function (Blueprint $table) {
            $table->dropColumn('afdracht_sponsorcommissie');
        });
    }
};
