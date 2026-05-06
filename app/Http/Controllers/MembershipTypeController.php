<?php

namespace App\Http\Controllers;

use App\Models\MembershipType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembershipTypeController extends Controller
{
    public function index(): View
    {
        $types = MembershipType::withCount('members')->orderBy('price_per_year')->get();

        return view('membership_types.index', compact('types'));
    }

    public function create(): View
    {
        return view('membership_types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validate($request);

        MembershipType::create($data);

        return redirect()->route('membership-types.index')->with('success', 'Pakket aangemaakt.');
    }

    public function edit(MembershipType $membershipType): View
    {
        return view('membership_types.edit', compact('membershipType'));
    }

    public function update(Request $request, MembershipType $membershipType): RedirectResponse
    {
        $data = $this->validate($request);

        $membershipType->update($data);

        return redirect()->route('membership-types.index')->with('success', 'Pakket bijgewerkt.');
    }

    public function destroy(MembershipType $membershipType): RedirectResponse
    {
        if ($membershipType->members()->exists()) {
            return back()->with('error', 'Dit pakket heeft nog actieve leden en kan niet verwijderd worden.');
        }

        $membershipType->delete();

        return redirect()->route('membership-types.index')->with('success', 'Pakket verwijderd.');
    }

    private function validate(Request $request): array
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price_per_year' => 'required|numeric|min:0',
            'max_members'    => 'nullable|integer|min:1',
            'is_active'      => 'boolean',
            'benefits'       => 'nullable|string',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        // Convert textarea (one benefit per line) to array
        if (!empty($data['benefits'])) {
            $data['benefits'] = array_values(array_filter(
                array_map('trim', explode("\n", $data['benefits']))
            ));
        } else {
            $data['benefits'] = [];
        }

        return $data;
    }
}
