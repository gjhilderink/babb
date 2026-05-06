<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $members = Member::with('membershipType')
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('company_name', 'like', "%$s%");
            }))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->membership_type_id, fn ($q, $id) => $q->where('membership_type_id', $id))
            ->orderBy('last_name')
            ->paginate(25)
            ->withQueryString();

        $membershipTypes = MembershipType::where('is_active', true)->orderBy('name')->get();

        return view('members.index', compact('members', 'membershipTypes'));
    }

    public function create(): View
    {
        $membershipTypes = MembershipType::where('is_active', true)->orderBy('name')->get();

        return view('members.create', compact('membershipTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name'         => 'required|string|max:255',
            'last_name'          => 'required|string|max:255',
            'email'              => 'required|email|unique:members,email',
            'phone'              => 'nullable|string|max:30',
            'company_name'       => 'nullable|string|max:255',
            'address'            => 'nullable|string|max:255',
            'postal_code'        => 'nullable|string|max:20',
            'city'               => 'nullable|string|max:255',
            'country'            => 'nullable|string|size:2',
            'membership_type_id' => 'nullable|exists:membership_types,id',
            'membership_start'   => 'nullable|date',
            'membership_end'     => 'nullable|date|after_or_equal:membership_start',
            'status'             => 'required|in:active,inactive,suspended',
            'notes'              => 'nullable|string',
        ]);

        Member::create($data);

        return redirect()->route('members.index')->with('success', 'Lid aangemaakt.');
    }

    public function show(Member $member): View
    {
        $member->load(['membershipType', 'invoices' => fn ($q) => $q->latest()->limit(10)]);

        return view('members.show', compact('member'));
    }

    public function edit(Member $member): View
    {
        $membershipTypes = MembershipType::where('is_active', true)->orderBy('name')->get();

        return view('members.edit', compact('member', 'membershipTypes'));
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $data = $request->validate([
            'first_name'         => 'required|string|max:255',
            'last_name'          => 'required|string|max:255',
            'email'              => 'required|email|unique:members,email,'.$member->id,
            'phone'              => 'nullable|string|max:30',
            'company_name'       => 'nullable|string|max:255',
            'address'            => 'nullable|string|max:255',
            'postal_code'        => 'nullable|string|max:20',
            'city'               => 'nullable|string|max:255',
            'country'            => 'nullable|string|size:2',
            'membership_type_id' => 'nullable|exists:membership_types,id',
            'membership_start'   => 'nullable|date',
            'membership_end'     => 'nullable|date|after_or_equal:membership_start',
            'status'             => 'required|in:active,inactive,suspended',
            'notes'              => 'nullable|string',
        ]);

        $member->update($data);

        return redirect()->route('members.show', $member)->with('success', 'Lid bijgewerkt.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $member->delete();

        return redirect()->route('members.index')->with('success', 'Lid verwijderd.');
    }
}
