<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('korting_type', 20)->nullable()->after('status'); // percentage | bedrag
            $table->decimal('korting_waarde', 10, 2)->nullable()->after('korting_type');
            $table->string('korting_reden')->nullable()->after('korting_waarde');
            $table->date('korting_vanaf')->nullable()->after('korting_reden');
            $table->date('korting_tot')->nullable()->after('korting_vanaf');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['korting_type', 'korting_waarde', 'korting_reden', 'korting_vanaf', 'korting_tot']);
        });
    }
};
