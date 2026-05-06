<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->string('source')->nullable();          // via welk kanaal (evenement, website, doorverwijzing...)
            $table->enum('status', ['nieuw', 'contact', 'follow_up', 'gewonnen', 'verloren'])->default('nieuw');
            $table->text('notes')->nullable();
            // Wie heeft deze lead aangemeld?
            $table->foreignId('referred_by_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('referred_by_name')->nullable();   // vrije tekst als het geen lid is
            // Wie volgt op?
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            // Conversie
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
