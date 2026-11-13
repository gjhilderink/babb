<?php

namespace App\Http\Controllers;

use App\Models\MembershipType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MembershipTypeController extends Controller
{
    public function index(): View
    {
        $types = MembershipType::with('prijzen')->withCount('members')->orderBy('price_per_year')->get();

        return view('membership_types.index', compact('types'));
    }

    public function create(): View
    {
        return view('membership_types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data    = $this->validate($request);
        $prijzen = $this->validatePrijzen($request);

        DB::transaction(function () use ($data, $prijzen) {
            $type = MembershipType::create($data);
            $this->syncPrijzen($type, $prijzen);
        });

        return redirect()->route('membership-types.index')->with('success', 'Pakket aangemaakt.');
    }

    public function edit(MembershipType $membershipType): View
    {
        $membershipType->load('prijzen');

        return view('membership_types.edit', compact('membershipType'));
    }

    public function update(Request $request, MembershipType $membershipType): RedirectResponse
    {
        $data    = $this->validate($request);
        $prijzen = $this->validatePrijzen($request);

        DB::transaction(function () use ($membershipType, $data, $prijzen) {
            $membershipType->update($data);
            $this->syncPrijzen($membershipType, $prijzen);
        });

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

    /** Bedragvelden van een jaarprijsregel; een leeg veld betekent "standaardbedrag van het pakket". */
    private const PRIJS_VELDEN = ['prijs', 'afdracht_sponsorcommissie', 'afdracht_bonboys'];

    /** Jaarprijzen uit het formulier; regels zonder enig bedrag worden genegeerd. */
    private function validatePrijzen(Request $request): array
    {
        $heeftBedrag = fn ($r) => collect(self::PRIJS_VELDEN)->contains(fn ($v) => filled($r[$v] ?? null));

        $request->merge([
            'prijzen' => collect($request->input('prijzen', []))
                ->filter(fn ($r) => filled($r['jaar'] ?? null) || $heeftBedrag($r))
                ->values()
                ->all(),
        ]);

        $request->validate([
            'prijzen'                             => 'array',
            'prijzen.*.jaar'                      => 'required|integer|min:2000|max:2100|distinct',
            'prijzen.*.prijs'                     => 'nullable|numeric|min:0',
            'prijzen.*.afdracht_sponsorcommissie' => 'nullable|numeric|min:0',
            'prijzen.*.afdracht_bonboys'          => 'nullable|numeric|min:0',
        ], [
            'prijzen.*.jaar.required' => 'Vul bij elke jaarprijs een jaar in.',
            'prijzen.*.jaar.distinct' => 'Elk jaar mag maar één keer voorkomen bij de jaarprijzen.',
        ]);

        return collect($request->input('prijzen', []))->filter($heeftBedrag)->values()->all();
    }

    private function syncPrijzen(MembershipType $type, array $prijzen): void
    {
        $type->prijzen()->delete();

        foreach ($prijzen as $r) {
            $velden = ['jaar' => (int) $r['jaar']];
            foreach (self::PRIJS_VELDEN as $v) {
                $velden[$v] = filled($r[$v] ?? null) ? $r[$v] : null;
            }

            $type->prijzen()->create($velden);
        }
    }

    private function validate(Request $request): array
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'price_per_year' => 'required|numeric|min:0',
            'afdracht_sponsorcommissie' => 'nullable|numeric|min:0',
            'afdracht_bonboys' => 'nullable|numeric|min:0',
            'max_members'    => 'nullable|integer|min:1',
            'is_active'      => 'boolean',
            'benefits'       => 'nullable|string',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['afdracht_sponsorcommissie'] = $data['afdracht_sponsorcommissie'] ?? 0;
        $data['afdracht_bonboys']          = $data['afdracht_bonboys'] ?? 0;

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
