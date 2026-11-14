{{-- Verzendknop voor één factuur (mail met PDF-bijlage). Verwacht: $invoice (met member geladen) --}}
@if ($invoice->status === 'cancelled')
    {{-- geannuleerd: niet verzenden --}}
@elseif ($invoice->ontvangerEmail())
    <form method="POST" action="{{ route('invoices.send', $invoice) }}" class="inline"
          onsubmit="return confirm(@js('Factuur '.$invoice->invoice_number.' verzenden naar '.$invoice->ontvangerEmail().'?'))">
        @csrf
        <button type="submit" class="text-xs text-blue-600 font-medium hover:underline">
            {{ $invoice->sent_at ? 'Opnieuw verzenden' : 'Verzenden' }}
        </button>
    </form>
@else
    <span class="text-xs text-gray-300 cursor-not-allowed" title="Lid heeft geen e-mailadres">Verzenden</span>
@endif
