<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Member;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembershipBillingController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService) {}

    public function index(Request $request): View
    {
        $members = Member::with('membershipType')
            ->where('status', 'active')
            ->whereNotNull('membership_type_id')
            ->whereHas('membershipType', fn ($q) => $q->where('is_active', true))
            ->orderBy('last_name')
            ->get()
            ->map(function (Member $member) {
                // Check if member already has an open invoice this year
                $member->already_invoiced = Invoice::where('member_id', $member->id)
                    ->whereYear('issue_date', now()->year)
                    ->whereIn('status', ['draft', 'sent', 'paid'])
                    ->exists();

                return $member;
            });

        $issueDate = $request->old('issue_date', now()->format('Y-m-d'));
        $dueDate   = $request->old('due_date', now()->addDays(30)->format('Y-m-d'));

        return view('membership_billing.index', compact('members', 'issueDate', 'dueDate'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'member_ids'   => 'required|array|min:1',
            'member_ids.*' => 'exists:members,id',
            'issue_date'   => 'required|date',
            'due_date'     => 'required|date|after_or_equal:issue_date',
        ]);

        $members = Member::with('membershipType')
            ->whereIn('id', $request->member_ids)
            ->where('status', 'active')
            ->whereNotNull('membership_type_id')
            ->get();

        $created = 0;

        foreach ($members as $member) {
            $type = $member->membershipType;

            $this->invoiceService->create([
                'member_id'  => $member->id,
                'issue_date' => $request->issue_date,
                'due_date'   => $request->due_date,
                'notes'      => "Lidmaatschapsfactuur {$type->name} — " . now()->year,
                'items'      => [
                    [
                        'product_id'  => null,
                        'description' => "Lidmaatschap {$type->name} " . now()->year,
                        'quantity'    => 1,
                        'unit_price'  => $type->price_per_year,
                        'tax_rate'    => 21,
                    ],
                ],
            ]);

            $created++;
        }

        return redirect()->route('invoices.index')
            ->with('success', "{$created} lidmaatschapsfactuur(en) aangemaakt.");
    }
}
