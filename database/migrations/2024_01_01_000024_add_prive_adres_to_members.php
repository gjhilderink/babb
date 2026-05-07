<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('prive_adres')->nullable()->after('city');
            $table->string('prive_postcode')->nullable()->after('prive_adres');
            $table->string('prive_stad')->nullable()->after('prive_postcode');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['prive_adres', 'prive_postcode', 'prive_stad']);
        });
    }
};
