<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('assigned_to_user_id')->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->date('due_date')->nullable();
            $table->enum('status', ['open', 'bezig', 'gereed'])->default('open');
            $table->enum('priority', ['laag', 'normaal', 'hoog'])->default('normaal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
