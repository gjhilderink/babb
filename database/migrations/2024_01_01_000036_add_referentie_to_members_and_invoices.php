<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('factuur_referentie')->nullable()->after('factuur_email');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('referentie')->nullable()->after('member_id');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('factuur_referentie');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('referentie');
        });
    }
};
