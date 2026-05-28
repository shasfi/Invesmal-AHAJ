<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('startups', function (Blueprint $table) {
            $table->decimal('funding_goal', 15, 2)->nullable()->after('stage');
            $table->decimal('amount_raised', 15, 2)->nullable()->default(0)->after('funding_goal');
            $table->decimal('equity_offered', 5, 2)->nullable()->after('amount_raised');
            $table->text('problem')->nullable()->after('description');
            $table->text('solution')->nullable()->after('problem');
            $table->text('market_opportunity')->nullable()->after('solution');
            $table->text('business_model')->nullable()->after('market_opportunity');
            $table->text('traction')->nullable()->after('business_model');
            $table->text('vision')->nullable()->after('traction');
            $table->string('mission', 255)->nullable()->after('name');
            $table->integer('active_users')->nullable()->after('team_size');
            $table->decimal('mrr', 12, 2)->nullable()->after('active_users');
            $table->decimal('growth_rate', 5, 2)->nullable()->after('mrr');
            $table->decimal('burn_rate', 12, 2)->nullable()->after('growth_rate');
            $table->integer('runway_months')->nullable()->after('burn_rate');
            $table->timestamp('funding_deadline')->nullable()->after('runway_months');
        });
    }

    public function down(): void
    {
        Schema::table('startups', function (Blueprint $table) {
            $table->dropColumn([
                'funding_goal',
                'amount_raised',
                'equity_offered',
                'problem',
                'solution',
                'market_opportunity',
                'business_model',
                'traction',
                'vision',
                'mission',
                'active_users',
                'mrr',
                'growth_rate',
                'burn_rate',
                'runway_months',
                'funding_deadline',
            ]);
        });
    }
};
