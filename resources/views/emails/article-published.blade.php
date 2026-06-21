@php
    $unsubscribeUrl = URL::signedRoute('newsletter.unsubscribe-articles', $subscriber);
@endphp
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
                                {{ $article->title }}
                            </h1>
                            <p style="margin:0 0 20px;font-size:14px;color:#6b7280;line-height:1.6;">
                                Bonjour <strong style="color:#111827;">{{ $subscriber->pseudo }}</strong>,<br>
                                Un nouvel article vient d'être publié sur le blog.
                            </p>
                            <div style="background-color:#f9fafb;border-radius:8px;padding:16px;margin-bottom:20px;">
                                <p style="margin:0;font-size:14px;color:#6b7280;line-height:1.6;">
                                    {{ $article->excerpt ?? Str::limit($article->content, 200) }}
                                </p>
                            </div>
                            <a href="{{ route('blog.show', $article->slug) }}"
                               style="display:inline-block;background-color:#4f46e5;color:#ffffff;padding:12px 24px;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;">
                                Lire l'article
                            </a>
                            <hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0;">
                            <p style="margin:0 0 4px;font-size:12px;color:#9ca3af;">
                                Vous recevez cet email car vous êtes abonné aux articles.
                            </p>
                            <a href="{{ $unsubscribeUrl }}"
                               style="font-size:12px;color:#4f46e5;text-decoration:underline;">
                                Se désabonner des articles
                            </a>
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
