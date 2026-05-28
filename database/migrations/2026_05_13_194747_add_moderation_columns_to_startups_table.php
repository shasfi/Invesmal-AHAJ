<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('startups', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false)->after('stage');
            $table->boolean('is_flagged')->default(false)->after('is_verified');
            $table->text('flag_reason')->nullable()->after('is_flagged');
            $table->foreignId('verified_by')->nullable()->after('flag_reason')->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable()->after('verified_by');
        });
    }

    public function down(): void
    {
        Schema::table('startups', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['is_verified', 'is_flagged', 'flag_reason', 'verified_by', 'verified_at']);
        });
    }
};