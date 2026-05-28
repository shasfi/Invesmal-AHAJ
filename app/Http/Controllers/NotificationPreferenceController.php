<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    public function edit(Request $request)
    {
        $preferences = NotificationPreference::firstOrCreate(
            ['user_id' => $request->user()->id],
            NotificationPreference::defaultAttributes()
        );

        return view('notification-preferences', compact('preferences'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'email_investment_updates' => ['nullable', 'boolean'],
            'email_meeting_updates' => ['nullable', 'boolean'],
            'email_message_notifications' => ['nullable', 'boolean'],
            'email_verification_updates' => ['nullable', 'boolean'],
            'in_app_investment_updates' => ['nullable', 'boolean'],
            'in_app_meeting_updates' => ['nullable', 'boolean'],
            'in_app_message_notifications' => ['nullable', 'boolean'],
            'in_app_verification_updates' => ['nullable', 'boolean'],
        ]);

        $preferences = NotificationPreference::firstOrCreate(
            ['user_id' => $request->user()->id],
            NotificationPreference::defaultAttributes()
        );

        $preferences->update([
            'email_investment_updates' => $request->boolean('email_investment_updates'),
            'email_meeting_updates' => $request->boolean('email_meeting_updates'),
            'email_message_notifications' => $request->boolean('email_message_notifications'),
            'email_verification_updates' => $request->boolean('email_verification_updates'),
            'in_app_investment_updates' => $request->boolean('in_app_investment_updates'),
            'in_app_meeting_updates' => $request->boolean('in_app_meeting_updates'),
            'in_app_message_notifications' => $request->boolean('in_app_message_notifications'),
            'in_app_verification_updates' => $request->boolean('in_app_verification_updates'),
        ]);

        return back()->with('success', 'Notification preferences saved.');
    }
}
