<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->title }}</title>
</head>
<body style="margin:0;padding:0;background-color:#1a1a2e;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#1a1a2e;padding:40px 20px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:540px;background-color:#16213e;border-radius:12px;overflow:hidden;border:1px solid #0f3460;">
                    <!-- Header -->
                    <tr>
                        <td style="padding:32px 32px 20px 32px;text-align:center;">
                            <h1 style="color:#e3d2c0;font-size:24px;font-weight:700;margin:0;letter-spacing:-0.02em;">
                                Invesmal
                            </h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:0 32px 24px 32px;">
                            <h2 style="color:#ffffff;font-size:18px;font-weight:600;margin:0 0 12px 0;">
                                {{ $notification->title }}
                            </h2>

                            @if($notification->body)
                            <p style="color:#a8a8b8;font-size:15px;line-height:1.6;margin:0 0 20px 0;">
                                {{ $notification->body }}
                            </p>
                            @endif

                            @if($notification->action_url)
                            <a href="{{ $notification->action_url }}"
                               style="display:inline-block;padding:12px 24px;background:linear-gradient(135deg,#184343,#1a6b6b);color:#e3d2c0;font-weight:600;font-size:14px;text-decoration:none;border-radius:8px;">
                                View Details
                            </a>
                            @endif
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding:0 32px;">
                            <hr style="border:none;border-top:1px solid #0f3460;margin:0;">
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:20px 32px 28px 32px;text-align:center;">
                            <p style="color:#666;font-size:12px;margin:0;">
                                &copy; {{ date('Y') }} Invesmal. All rights reserved.
                            </p>
                            <p style="color:#555;font-size:11px;margin:6px 0 0 0;">
                                You received this email because you have an Invesmal account.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>