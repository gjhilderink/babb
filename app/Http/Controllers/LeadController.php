<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Member;
use App\Models\MembershipType;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $leads = Lead::with(['referredByMember', 'assignedTo'])
            ->when($request->search, function ($q, $s) {
                $q->where(function ($q) use ($s) {
                    $q->where('first_name', 'like', "%$s%")
                      ->orWhere('last_name',  'like', "%$s%")
                      ->orWhere('email',      'like', "%$s%")
                      ->orWhere('company_name','like', "%$s%");
                });
            })
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->assigned_to, fn ($q, $id) => $q->where('assigned_to_user_id', $id))
            ->orderByRaw("FIELD(status,'nieuw','contact','follow_up','gewonnen','verloren')")
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $users = User::orderBy('name')->get();

        return view('leads.index', compact('leads', 'users'));
    }

    public function create(): View
    {
        $members = Member::where('status', 'active')->orderBy('last_name')->get();
        $users   = User::orderBy('name')->get();

        return view('leads.create', compact('members', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Lead::create($data);

        return redirect()->route('leads.index')->with('success', 'Lead aangemaakt.');
    }

    public function show(Lead $lead): View
    {
        $lead->load(['referredByMember', 'assignedTo', 'member']);

        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead): View
    {
        $members = Member::where('status', 'active')->orderBy('last_name')->get();
        $users   = User::orderBy('name')->get();

        return view('leads.edit', compact('lead', 'members', 'users'));
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $lead->update($this->validated($request));

        return redirect()->route('leads.show', $lead)->with('success', 'Lead bijgewerkt.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead verwijderd.');
    }

    public function updateStatus(Request $request, Lead $lead): RedirectResponse
    {
        $request->validate(['status' => 'required|in:nieuw,contact,follow_up,gewonnen,verloren']);
        $lead->update(['status' => $request->status]);
        return back()->with('success', 'Lead status bijgewerkt.');
    }

    public function convertForm(Lead $lead): View
    {
        if ($lead->isConverted()) {
            return redirect()->route('members.show', $lead->member);
        }

        $membershipTypes = MembershipType::where('is_active', true)->orderBy('name')->get();

        return view('leads.convert', compact('lead', 'membershipTypes'));
    }

    public function convert(Request $request, Lead $lead): RedirectResponse
    {
        if ($lead->isConverted()) {
            return redirect()->route('members.show', $lead->member);
        }

        $data = $request->validate([
            'first_name'         => 'required|string|max:255',
            'last_name'          => 'required|string|max:255',
            'email'              => 'required|email|unique:members,email',
            'phone'              => 'nullable|string|max:50',
            'company_name'       => 'nullable|string|max:255',
            'address'            => 'nullable|string|max:255',
            'postal_code'        => 'nullable|string|max:20',
            'city'               => 'nullable|string|max:100',
            'membership_type_id' => 'nullable|exists:membership_types,id',
            'membership_start'   => 'nullable|date',
            'membership_end'     => 'nullable|date|after_or_equal:membership_start',
            'notes'              => 'nullable|string',
        ]);

        $member = Member::create(array_merge($data, ['status' => 'active']));

        $lead->update([
            'status'       => 'gewonnen',
            'member_id'    => $member->id,
            'converted_at' => now(),
        ]);

        return redirect()->route('members.show', $member)
            ->with('success', "Lead {$lead->full_name} omgezet naar lid.");
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'first_name'             => 'required|string|max:255',
            'last_name'              => 'required|string|max:255',
            'email'                  => 'nullable|email|max:255',
            'phone'                  => 'nullable|string|max:50',
            'company_name'           => 'nullable|string|max:255',
            'source'                 => 'nullable|string|max:255',
            'status'                 => 'required|in:nieuw,contact,follow_up,gewonnen,verloren',
            'notes'                  => 'nullable|string',
            'referred_by_member_id'  => 'nullable|exists:members,id',
            'referred_by_name'       => 'nullable|string|max:255',
            'assigned_to_user_id'    => 'nullable|exists:users,id',
        ]);
    }
}
