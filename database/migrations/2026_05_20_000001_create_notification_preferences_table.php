<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('email_investment_updates')->default(true);
            $table->boolean('email_meeting_updates')->default(true);
            $table->boolean('email_message_notifications')->default(true);
            $table->boolean('email_verification_updates')->default(true);
            $table->boolean('in_app_investment_updates')->default(true);
            $table->boolean('in_app_meeting_updates')->default(true);
            $table->boolean('in_app_message_notifications')->default(true);
            $table->boolean('in_app_verification_updates')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};