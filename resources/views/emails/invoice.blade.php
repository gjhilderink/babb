<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Factuur {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 32px 16px; color: #1f2937; }
        .card { background: #ffffff; border-radius: 12px; max-width: 560px; margin: 0 auto; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .stripe { height: 4px; display: flex; }
        .stripe-red { flex: 1; background: #cc1c1c; }
        .stripe-green { flex: 1; background: #5ea31f; }
        .body { padding: 36px 40px; font-size: 14px; line-height: 1.6; color: #374151; }
    </style>
</head>
<body>
    <div class="card">
        <div class="stripe"><div class="stripe-red"></div><div class="stripe-green"></div></div>
        <div class="body">{!! nl2br(e($tekst)) !!}</div>
    </div>
</body>
</html>
