<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('declaraties', function (Blueprint $table) {
            $table->id();
            $table->string('omschrijving');
            $table->decimal('bedrag', 10, 2);
            $table->date('datum');
            $table->string('categorie')->nullable();
            $table->string('bijlage_path')->nullable();
            $table->string('bijlage_naam')->nullable();
            $table->string('status')->default('ingediend');
            $table->text('notities')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('declaraties');
    }
};
