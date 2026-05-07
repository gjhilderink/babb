<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('afdrachten', function (Blueprint $table) {
            $table->id();
            $table->string('onderwerp');
            $table->decimal('bedrag', 10, 2);
            $table->string('status')->default('nieuw');
            $table->date('datum')->nullable();
            $table->text('notities')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('afdrachten');
    }
};
