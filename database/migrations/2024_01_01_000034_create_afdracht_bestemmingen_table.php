<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('afdracht_bestemmingen', function (Blueprint $table) {
            $table->id();
            $table->string('soort', 30);
            $table->unsignedSmallInteger('jaar');
            $table->text('bestemming');
            $table->timestamps();

            $table->unique(['soort', 'jaar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('afdracht_bestemmingen');
    }
};
