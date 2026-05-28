<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pitch_decks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('startup_description')->nullable()->comment('User-provided startup description for generation');
            $table->longText('content_json')->nullable()->comment('Generated/edited pitch deck content as JSON (sections array)');
            $table->string('file_path')->nullable()->comment('Uploaded PPTX/PDF path in S3');
            $table->string('file_type')->nullable()->comment('pdf or pptx');
            $table->enum('status', ['draft', 'generated', 'analyzed', 'final'])->default('draft');
            $table->json('ai_analysis')->nullable()->comment('AI analysis results: scores, suggestions, market data');
            $table->json('ai_summary')->nullable()->comment('Public-facing summary excerpt');
            $table->integer('ai_score')->nullable()->comment('Overall AI pitch score 0-100');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pitch_decks');
    }
};