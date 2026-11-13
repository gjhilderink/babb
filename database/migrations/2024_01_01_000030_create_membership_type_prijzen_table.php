<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_type_prijzen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_type_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('jaar');
            $table->decimal('prijs', 10, 2)->nullable();
            $table->decimal('afdracht_sponsorcommissie', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['membership_type_id', 'jaar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_type_prijzen');
    }
};
