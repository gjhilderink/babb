<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Factuur {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        .header { display: flex; justify-content: space-between; margin-bottom: 32px; }
        .logo { font-size: 22px; font-weight: bold; color: #4f46e5; }
        .logo img { max-height: 60px; max-width: 200px; }
        .invoice-meta { text-align: right; }
        .invoice-meta h2 { font-size: 20px; font-weight: bold; margin: 0 0 8px; }
        .parties { display: flex; justify-content: space-between; margin-bottom: 32px; }
        .party h3 { font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th { background: #f9fafb; text-align: right; padding: 8px 10px; font-size: 11px; color: #6b7280; font-weight: 600; border-bottom: 1px solid #e5e7eb; }
        th:first-child { text-align: left; }
        td { padding: 8px 10px; border-bottom: 1px solid #f3f4f6; text-align: right; }
        td:first-child { text-align: left; }
        tfoot td { font-weight: bold; border-top: 2px solid #e5e7eb; }
        .total-row td { font-size: 14px; background: #f9fafb; }
        .notes { color: #6b7280; font-size: 11px; margin-top: 16px; }
        .footer { border-top: 1px solid #e5e7eb; margin-top: 32px; padding-top: 12px; color: #6b7280; font-size: 10px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            @if ($invoiceLogoBase64)
                <img src="{{ $invoiceLogoBase64 }}" alt="Logo">
            @else
                BABB Portaal
            @endif
        </div>
        <div class="invoice-meta">
            <h2>FACTUUR</h2>
            <div>{{ $invoice->invoice_number }}</div>
            <div>Datum: {{ $invoice->issue_date->format('d-m-Y') }}</div>
            <div>Vervaldatum: {{ $invoice->due_date->format('d-m-Y') }}</div>
        </div>
    </div>

    <div class="parties">
        <div class="party">
            <h3>Aan</h3>
            <strong>{{ $invoice->member->full_name }}</strong><br>
            @if ($invoice->member->company_name){{ $invoice->member->company_name }}<br>@endif
            @if ($invoice->member->address){{ $invoice->member->address }}<br>@endif
            @if ($invoice->member->postal_code || $invoice->member->city)
                {{ $invoice->member->postal_code }} {{ $invoice->member->city }}<br>
            @endif
            {{ $invoice->member->email }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Omschrijving</th>
                <th>Aantal</th>
                <th>Stukprijs</th>
                <th>BTW %</th>
                <th>Totaal incl.</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td>{{ number_format($item->quantity, 2, ',', '.') }}</td>
                <td>&euro; {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                <td>{{ $item->tax_rate }}%</td>
                <td>&euro; {{ number_format($item->line_total + $item->tax_amount, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Subtotaal</td>
                <td>&euro; {{ number_format($invoice->subtotal, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4">BTW</td>
                <td>&euro; {{ number_format($invoice->tax_amount, 2, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="4">Totaal</td>
                <td>&euro; {{ number_format($invoice->total, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    @if ($invoice->notes)
    <div class="notes">Notities: {{ $invoice->notes }}</div>
    @endif

    @if ($invoiceFooter)
    <div class="footer">{{ $invoiceFooter }}</div>
    @endif
</body>
</html>
