<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('event_date');
            $table->dateTime('event_end')->nullable();
            $table->string('location')->nullable();
            $table->enum('status', ['concept', 'bevestigd', 'afgerond', 'geannuleerd'])->default('concept');
            $table->unsignedInteger('max_attendees')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('event_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->string('assigned_to')->nullable();
            $table->enum('status', ['open', 'bezig', 'gereed'])->default('open');
            $table->date('due_date')->nullable();
            $table->timestamps();
        });

        Schema::create('event_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->string('category')->nullable();
            $table->string('paid_by')->nullable();
            $table->date('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_costs');
        Schema::dropIfExists('event_tasks');
        Schema::dropIfExists('events');
    }
};
