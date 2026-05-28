<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('startup_id')->nullable()->constrained('startups')->cascadeOnDelete();
            $table->enum('type', ['pitch_deck', 'business_plan', 'other'])->default('other');
            $table->string('filename');
            $table->string('original_name');
            $table->string('path');
            $table->integer('version')->default(1);
            $table->bigInteger('size')->default(0);
            $table->string('mime_type', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};