<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evenement_reserveringen', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('jaar')->unique();
            $table->decimal('bedrag_per_lid', 10, 2);
            $table->string('omschrijving')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evenement_reserveringen');
    }
};
