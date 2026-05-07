<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welkom bij het BABB Portaal</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 32px 16px; color: #1f2937; }
        .card { background: #ffffff; border-radius: 12px; max-width: 520px; margin: 0 auto; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .stripe { height: 4px; display: flex; }
        .stripe-red { flex: 1; background: #cc1c1c; }
        .stripe-green { flex: 1; background: #5ea31f; }
        .body { padding: 36px 40px; }
        h1 { font-size: 22px; font-weight: 700; margin: 0 0 8px; }
        p { font-size: 14px; line-height: 1.6; color: #374151; margin: 0 0 16px; }
        .credentials { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px 20px; margin: 20px 0; }
        .credentials table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .credentials td { padding: 5px 0; }
        .credentials td:first-child { color: #6b7280; width: 110px; }
        .credentials td:last-child { font-weight: 600; }
        .btn { display: inline-block; background: #5ea31f; color: #ffffff !important; text-decoration: none; font-size: 14px; font-weight: 600; padding: 12px 28px; border-radius: 8px; margin: 8px 0 24px; }
        .footer { font-size: 12px; color: #9ca3af; border-top: 1px solid #f3f4f6; padding-top: 20px; margin-top: 8px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="stripe">
            <div class="stripe-red"></div>
            <div class="stripe-green"></div>
        </div>
        <div class="body">
            <h1>Welkom, {{ $user->name }}!</h1>
            <p>Je account voor het <strong>BABB Portaal</strong> is aangemaakt. Hieronder vind je je inloggegevens.</p>

            <div class="credentials">
                <table>
                    <tr>
                        <td>E-mailadres</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td>Wachtwoord</td>
                        <td>{{ $plainPassword }}</td>
                    </tr>
                    <tr>
                        <td>Rol</td>
                        <td>{{ $user->roleName() }}</td>
                    </tr>
                </table>
            </div>

            <p>Klik op de knop hieronder om in te loggen. We raden aan je wachtwoord na de eerste login te wijzigen.</p>

            <a href="{{ $portalUrl }}" class="btn">Naar het portaal</a>

            <p class="footer">
                Dit bericht is automatisch verzonden. Heb je vragen? Neem contact op met de beheerder.<br>
                {{ $portalUrl }}
            </p>
        </div>
    </div>
</body>
</html>
