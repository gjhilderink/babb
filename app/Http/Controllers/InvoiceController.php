<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Member;
use App\Models\Product;
use App\Services\AclService;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService) {}

    public function index(Request $request): View
    {
        abort_unless(AclService::allowed('invoices.view'), 403);

        $invoices = Invoice::with('member')
            ->when($request->search, fn ($q, $s) => $q->where('invoice_number', 'like', "%$s%")
                ->orWhereHas('member', fn ($q) => $q->where('last_name', 'like', "%$s%")
                    ->orWhere('company_name', 'like', "%$s%")))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->member_id, fn ($q, $id) => $q->where('member_id', $id))
            ->latest('issue_date')
            ->paginate(25)
            ->withQueryString();

        return view('invoices.index', compact('invoices'));
    }

    public function create(Request $request): View
    {
        abort_unless(AclService::allowed('invoices.create'), 403);

        $members  = Member::where('status', 'active')->orderBy('last_name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $member   = $request->member_id ? Member::find($request->member_id) : null;

        return view('invoices.create', compact('members', 'products', 'member'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(AclService::allowed('invoices.create'), 403);

        $data = $request->validate([
            'member_id'  => 'required|exists:members,id',
            'issue_date' => 'required|date',
            'due_date'   => 'required|date|after_or_equal:issue_date',
            'notes'      => 'nullable|string',
            'items'      => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
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
            'issue_date' => 'required|date',
            'due_date'   => 'required|date|after_or_equal:issue_date',
            'notes'      => 'nullable|string',
            'items'      => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_price'  => 'required|numeric|min:0',
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
