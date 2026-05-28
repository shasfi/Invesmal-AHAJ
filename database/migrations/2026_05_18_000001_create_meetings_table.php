<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('meetings')) {
            return;
        }
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduler_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('invitee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('startup_id')->nullable()->constrained('startups')->nullOnDelete();
            $table->string('title');
            $table->text('notes')->nullable();
            $table->string('location')->nullable();
            $table->dateTime('scheduled_at');
            $table->string('status')->default('pending'); // pending, accepted, declined, cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};