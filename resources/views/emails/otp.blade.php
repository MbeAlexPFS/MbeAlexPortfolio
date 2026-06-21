<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0;padding:0;background-color:#f9fafb;font-family:'Instrument Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f9fafb;">
        <tr>
            <td align="center" style="padding:40px 16px;">
                <table width="480" cellpadding="0" cellspacing="0" style="max-width:480px;width:100%;">
                    <tr>
                        <td style="padding-bottom:24px;text-align:center;">
                            <a href="{{ route('home') }}" style="font-size:22px;font-weight:600;color:#4f46e5;text-decoration:none;">
                                MbeAlex<span style="color:#9ca3af;">.</span>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#ffffff;border-radius:12px;border:1px solid #e5e7eb;padding:32px;">
                            <h1 style="margin:0 0 8px;font-size:20px;font-weight:700;color:#111827;">
                                Vérification de votre compte
                            </h1>
                            <p style="margin:0 0 20px;font-size:14px;color:#6b7280;line-height:1.6;">
                                Bonjour <strong style="color:#111827;">{{ $user->pseudo }}</strong>,<br>
                                Voici votre code de vérification à usage unique :
                            </p>
                            <div style="background-color:#f3f4f6;border-radius:8px;padding:20px;text-align:center;margin-bottom:20px;">
                                <span style="font-size:36px;font-weight:700;color:#4f46e5;letter-spacing:8px;font-family:monospace;">
                                    {{ $otp }}
                                </span>
                            </div>
                            <p style="margin:0 0 4px;font-size:13px;color:#9ca3af;">
                                Ce code expire dans 15 minutes.
                            </p>
                            <p style="margin:0;font-size:13px;color:#9ca3af;">
                                Si vous n'avez pas créé de compte, ignorez cet email.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top:24px;text-align:center;font-size:12px;color:#9ca3af;">
                            &copy; {{ date('Y') }} MbeAlex &mdash; Tous droits réservés.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
