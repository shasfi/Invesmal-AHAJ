<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class DevMailHelper
{
    public static function usesLogDriver(): bool
    {
        return config('mail.default') === 'log';
    }

    /**
     * Build password reset URL for local dev when mail goes to log file only.
     */
    public static function passwordResetUrl(string $email): ?string
    {
        if (!self::usesLogDriver()) {
            return null;
        }

        $row = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$row || empty($row->token)) {
            return null;
        }

        return URL::route('password.reset', [
            'token' => $row->token,
            'email' => $email,
        ]);
    }
}
