<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('startups', function (Blueprint $table) {
            $table->foreignId('pitch_deck_id')->nullable()->after('stage')
                ->constrained('pitch_decks')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('startups', function (Blueprint $table) {
            $table->dropForeign(['pitch_deck_id']);
            $table->dropColumn('pitch_deck_id');
        });
    }
};