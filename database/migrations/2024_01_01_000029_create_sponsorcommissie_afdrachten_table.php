<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsorcommissie_afdrachten', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('jaar');
            $table->decimal('bedrag', 10, 2);
            $table->date('overgemaakt_op');
            $table->timestamps();

            $table->unique(['member_id', 'jaar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsorcommissie_afdrachten');
    }
};
