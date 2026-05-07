<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Declaratie</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 32px 16px; color: #1f2937; }
        .card { background: #ffffff; border-radius: 12px; max-width: 560px; margin: 0 auto; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .stripe { height: 4px; display: flex; }
        .stripe-red { flex: 1; background: #cc1c1c; }
        .stripe-green { flex: 1; background: #5ea31f; }
        .body { padding: 36px 40px; }
        h1 { font-size: 20px; font-weight: 700; margin: 0 0 8px; }
        p { font-size: 14px; line-height: 1.6; color: #374151; margin: 0 0 16px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; margin: 20px 0; }
        th { text-align: left; padding: 6px 8px; background: #f9fafb; color: #6b7280; border-bottom: 1px solid #e5e7eb; }
        td { padding: 8px; border-bottom: 1px solid #f3f4f6; }
        .total { font-weight: 700; }
        .footer { font-size: 12px; color: #9ca3af; border-top: 1px solid #f3f4f6; padding-top: 16px; margin-top: 8px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="stripe"><div class="stripe-red"></div><div class="stripe-green"></div></div>
        <div class="body">
            <h1>Declaratie &mdash; {{ $event->title }}</h1>
            <p>Bijgevoegd de bonnen/bijlagen voor onderstaande kostenposten van het evenement op {{ $event->event_date->format('d-m-Y') }}.</p>

            <table>
                <thead>
                    <tr>
                        <th>Omschrijving</th>
                        <th>Categorie</th>
                        <th style="text-align:right">Bedrag</th>
                        <th>Betaald door</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($event->costs as $cost)
                    @if ($cost->receipt_path)
                    <tr>
                        <td>{{ $cost->description }}</td>
                        <td>{{ $cost->category ?? '&mdash;' }}</td>
                        <td style="text-align:right">&euro; {{ number_format($cost->amount, 2, ',', '.') }}</td>
                        <td>{{ $cost->paid_by ?? '&mdash;' }}</td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="total">Totaal</td>
                        <td style="text-align:right" class="total">
                            &euro; {{ number_format($event->costs->whereNotNull('receipt_path')->sum('amount'), 2, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <p class="footer">Dit bericht is automatisch verzonden vanuit het BABB Portaal.</p>
        </div>
    </div>
</body>
</html>
