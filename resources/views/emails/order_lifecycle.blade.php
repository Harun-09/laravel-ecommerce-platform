<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background:#f5f7fb;font-family:Arial,sans-serif;color:#111827;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f5f7fb;padding:20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="640" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="padding:24px;background:#0f172a;color:#ffffff;">
                            <h1 style="margin:0;font-size:20px;line-height:1.4;">{{ config('app.name', 'NovaMart') }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 24px 10px;">
                            <p style="margin:0 0 16px;font-size:16px;font-weight:600;">{{ $greeting ?? 'Hello,' }}</p>
                            {!! $bodyHtml !!}
                            @if(!empty($actionUrl))
                                <p style="margin:20px 0 0;">
                                    <a href="{{ $actionUrl }}" style="display:inline-block;padding:10px 18px;background:#0f172a;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:600;">
                                        {{ $actionText ?? 'View Order' }}
                                    </a>
                                </p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px 24px;color:#6b7280;font-size:12px;">
                            <p style="margin:0;">This is an automated notification from {{ config('app.name', 'NovaMart') }}.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
