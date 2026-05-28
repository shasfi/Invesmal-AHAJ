<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('startups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('founder_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('industry')->nullable();
            $table->enum('stage', ['idea', 'mvp', 'funded'])->default('idea');
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->integer('team_size')->nullable()->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('startups');
    }
};