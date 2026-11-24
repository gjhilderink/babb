<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Product;
use App\Services\AclService;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService) {}

    public function index(Request $request): View
    {
        abort_unless(AclService::allowed('invoices.view'), 403);

        $basis = Invoice::query()
            ->when($request->search, fn ($q, $s) => $q->where(fn ($q) => $q->where('invoice_number', 'like', "%$s%")
                ->orWhereHas('member', fn ($q) => $q->where('last_name', 'like', "%$s%")
                    ->orWhere('company_name', 'like', "%$s%"))))
            ->when($request->member_id, fn ($q, $id) => $q->where('member_id', $id));

        // Aantallen per status voor de filterknoppen (binnen de zoekopdracht)
        $aantallen = collect(array_keys(self::STATUSSEN))->mapWithKeys(fn ($s) => [
            $s => $this->filterStatus(clone $basis, $s)->count(),
        ])->put('', (clone $basis)->count());

        $invoices = $this->filterStatus($basis->with('member'), $request->status)
            ->latest('issue_date')
            ->paginate(25)
            ->withQueryString();

        $statussen = self::STATUSSEN;

        return view('invoices.index', compact('invoices', 'aantallen', 'statussen'));
    }

    public const STATUSSEN = [
        'draft'     => 'Concept',
        'sent'      => 'Verstuurd',
        'overdue'   => 'Verlopen',
        'paid'      => 'Betaald',
        'cancelled' => 'Geannuleerd',
    ];

    /** "Verlopen" = status verlopen, of verstuurd en de vervaldatum is verstreken (zoals in de lijst getoond). */
    private function filterStatus($query, ?string $status)
    {
        return match ($status) {
            null, ''  => $query,
            'overdue' => $query->where(fn ($q) => $q->where('status', 'overdue')
                ->orWhere(fn ($q) => $q->where('status', 'sent')->whereDate('due_date', '<', today()))),
            'sent'    => $query->where('status', 'sent')->whereDate('due_date', '>=', today()),
            default   => $query->where('status', $status),
        };
    }

    /** Eén factuur per e-mail versturen, met de PDF als bijlage. */
    public function send(Invoice $invoice): RedirectResponse
    {
        abort_unless(AclService::allowed('invoices.edit'), 403);

        $invoice->load('member');

        if (! $invoice->kanVerzonden()) {
            return back()->with('error', $invoice->status === 'cancelled'
                ? 'Een geannuleerde factuur kan niet worden verzonden.'
                : "Lid {$invoice->member?->full_name} heeft geen e-mailadres.");
        }

        try {
            $this->verzend($invoice);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', "Verzenden van factuur {$invoice->invoice_number} mislukt: {$e->getMessage()}");
        }

        return back()->with('success', "Factuur {$invoice->invoice_number} verzonden naar {$invoice->sent_to}.");
    }

    /** Meerdere geselecteerde facturen in één keer versturen. */
    public function sendBulk(Request $request): RedirectResponse
    {
        abort_unless(AclService::allowed('invoices.edit'), 403);

        $data = $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'integer',
        ], ['ids.required' => 'Selecteer eerst een of meer facturen.']);

        $verzonden = 0;
        $overgeslagen = [];
        $mislukt = [];

        foreach (Invoice::with('member')->whereIn('id', $data['ids'])->orderBy('invoice_number')->get() as $invoice) {
            if (! $invoice->kanVerzonden()) {
                $overgeslagen[] = $invoice->invoice_number;

                continue;
            }

            try {
                $this->verzend($invoice);
                $verzonden++;
            } catch (\Throwable $e) {
                report($e);
                $mislukt[] = $invoice->invoice_number;
            }
        }

        $melding = "{$verzonden} factuur/facturen verzonden.";
        if ($overgeslagen) {
            $melding .= ' Overgeslagen (geen e-mailadres of geannuleerd): '.implode(', ', $overgeslagen).'.';
        }

        return back()->with($mislukt ? 'error' : 'success', $mislukt
            ? $melding.' Mislukt: '.implode(', ', $mislukt).'.'
            : $melding);
    }

    private function verzend(Invoice $invoice): void
    {
        $naar = $invoice->ontvangerEmail();

        Mail::to($naar)->send(new InvoiceMail($invoice));

        $invoice->update([
            'sent_at' => now(),
            'sent_to' => $naar,
            'status'  => $invoice->status === 'draft' ? 'sent' : $invoice->status,
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless(AclService::allowed('invoices.create'), 403);

        $members  = Member::with('membershipType.prijzen')->where('status', 'active')->orderBy('last_name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $member   = $request->member_id ? Member::find($request->member_id) : null;

        // Lidmaatschapspakket per lid, om de factuurregel automatisch te vullen.
        // De prijs hangt af van het seizoen van de factuurdatum: 'prijzen' = vastgelegde
        // seizoensprijzen, 'price' = standaardprijs voor seizoenen zonder eigen prijs.
        $memberships = $members->filter(fn (Member $m) => $m->membershipType)->mapWithKeys(fn (Member $m) => [$m->id => [
            'name'    => $m->membershipType->name,
            'price'   => (float) $m->membershipType->price_per_year,
            'prijzen' => $m->membershipType->prijzen->whereNotNull('prijs')
                ->mapWithKeys(fn ($p) => [$p->jaar => (float) $p->prijs]),
        ]]);

        // Tijdelijke kortingen per lid, voor de voorvertoning in het formulier
        $discounts = $members->filter->hasDiscount()->mapWithKeys(fn (Member $m) => [$m->id => [
            'type'   => $m->korting_type,
            'waarde' => (float) $m->korting_waarde,
            'label'  => $m->korting_label,
            'reden'  => $m->korting_reden,
            'vanaf'  => $m->korting_vanaf?->format('Y-m-d'),
            'tot'    => $m->korting_tot?->format('Y-m-d'),
        ]]);

        // Vaste factuurreferentie per lid, om het referentieveld automatisch te vullen
        $referenties = $members->filter(fn (Member $m) => filled($m->factuur_referentie))
            ->mapWithKeys(fn (Member $m) => [$m->id => $m->factuur_referentie]);

        return view('invoices.create', compact('members', 'products', 'member', 'discounts', 'memberships', 'referenties'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(AclService::allowed('invoices.create'), 403);

        $data = $request->validate([
            'member_id'  => 'required|exists:members,id',
            'referentie' => 'nullable|string|max:255',
            'issue_date' => 'required|date',
            'due_date'   => 'required|date|after_or_equal:issue_date',
            'notes'      => 'nullable|string',
            'apply_discount' => 'nullable|boolean',
            'items'      => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric',
            'items.*.tax_rate'    => 'required|numeric|min:0|max:100',
            'items.*.product_id'  => 'nullable|exists:products,id',
        ]);

        $invoice = $this->invoiceService->create($data);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Factuur aangemaakt.');
    }

    public function show(Invoice $invoice): View
    {
        abort_unless(AclService::allowed('invoices.view'), 403);

        $invoice->load(['member', 'items.product']);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice): View
    {
        abort_unless(AclService::allowed('invoices.edit'), 403);
        abort_if($invoice->status === 'paid', 403, 'Een betaalde factuur kan niet bewerkt worden.');

        $members  = Member::where('status', 'active')->orderBy('last_name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $invoice->load('items.product');

        return view('invoices.edit', compact('invoice', 'members', 'products'));
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_unless(AclService::allowed('invoices.edit'), 403);
        abort_if($invoice->status === 'paid', 403, 'Een betaalde factuur kan niet bewerkt worden.');

        $data = $request->validate([
            'member_id'  => 'required|exists:members,id',
            'referentie' => 'nullable|string|max:255',
            'issue_date' => 'required|date',
            'due_date'   => 'required|date|after_or_equal:issue_date',
            'notes'      => 'nullable|string',
            'items'      => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric',
            'items.*.tax_rate'    => 'required|numeric|min:0|max:100',
            'items.*.product_id'  => 'nullable|exists:products,id',
        ]);

        $this->invoiceService->update($invoice, $data);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Factuur bijgewerkt.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        abort_unless(AclService::allowed('invoices.delete'), 403);
        abort_if($invoice->status === 'paid', 403, 'Een betaalde factuur kan niet verwijderd worden.');

        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Factuur verwijderd.');
    }

    public function markPaid(Invoice $invoice): RedirectResponse
    {
        abort_unless(AclService::allowed('invoices.edit'), 403);

        $invoice->update(['status' => 'paid', 'paid_at' => now()]);

        return back()->with('success', 'Factuur gemarkeerd als betaald.');
    }

    public function markSent(Invoice $invoice): RedirectResponse
    {
        abort_if($invoice->status !== 'draft', 403, 'Alleen concept-facturen kunnen worden verstuurd.');

        $invoice->update(['status' => 'sent']);

        return back()->with('success', 'Factuur gemarkeerd als verstuurd.');
    }

    public function pdf(Invoice $invoice): Response
    {
        abort_unless(AclService::allowed('invoices.view'), 403);

        $invoice->load(['member', 'items.product']);

        return $this->invoiceService->generatePdf($invoice);
    }
}
