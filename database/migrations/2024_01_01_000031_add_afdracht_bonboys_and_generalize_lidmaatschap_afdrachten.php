<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_types', function (Blueprint $table) {
            $table->decimal('afdracht_bonboys', 10, 2)->default(0)->after('afdracht_sponsorcommissie');
        });

        Schema::table('membership_type_prijzen', function (Blueprint $table) {
            $table->decimal('afdracht_bonboys', 10, 2)->nullable()->after('afdracht_sponsorcommissie');
        });

        // De tabel voor "overgemaakt"-registraties wordt generiek: per soort afdracht.
        Schema::rename('sponsorcommissie_afdrachten', 'lidmaatschap_afdrachten');

        Schema::table('lidmaatschap_afdrachten', function (Blueprint $table) {
            $table->string('soort', 30)->default('sponsorcommissie')->after('member_id');
            // Eerst de nieuwe unieke index (begint met member_id, dus de foreign key blijft gedekt), dan de oude weg.
            $table->unique(['member_id', 'jaar', 'soort'], 'lidmaatschap_afdrachten_member_jaar_soort_unique');
            $table->dropUnique('sponsorcommissie_afdrachten_member_id_jaar_unique');
        });
    }

    public function down(): void
    {
        Schema::table('lidmaatschap_afdrachten', function (Blueprint $table) {
            $table->unique(['member_id', 'jaar'], 'sponsorcommissie_afdrachten_member_id_jaar_unique');
            $table->dropUnique('lidmaatschap_afdrachten_member_jaar_soort_unique');
            $table->dropColumn('soort');
        });

        Schema::rename('lidmaatschap_afdrachten', 'sponsorcommissie_afdrachten');

        Schema::table('membership_type_prijzen', function (Blueprint $table) {
            $table->dropColumn('afdracht_bonboys');
        });

        Schema::table('membership_types', function (Blueprint $table) {
            $table->dropColumn('afdracht_bonboys');
        });
    }
};
