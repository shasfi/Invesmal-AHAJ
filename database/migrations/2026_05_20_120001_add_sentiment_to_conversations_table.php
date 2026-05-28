<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->unsignedTinyInteger('sentiment_score')->nullable()->after('subject');
            $table->string('sentiment_label', 20)->nullable()->after('sentiment_score');
            $table->json('sentiment_breakdown')->nullable()->after('sentiment_label');
            $table->timestamp('sentiment_analyzed_at')->nullable()->after('sentiment_breakdown');
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn([
                'sentiment_score',
                'sentiment_label',
                'sentiment_breakdown',
                'sentiment_analyzed_at',
            ]);
        });
    }
};
