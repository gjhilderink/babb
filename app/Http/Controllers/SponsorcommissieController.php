<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\SponsorcommissieAfdracht;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SponsorcommissieController extends Controller
{
    public function index(Request $request): View
    {
        $jaren = $this->jaren();
        $jaar  = (int) $request->input('jaar', now()->year);
        if (! in_array($jaar, $jaren, true)) {
            $jaar = now()->year;
        }

        $regels = $this->regelsVoorJaar($jaar);

        $overzicht = collect($jaren)->map(function (int $j) use ($jaar, $regels) {
            $r = $j === $jaar ? $regels : $this->regelsVoorJaar($j);

            return [
                'jaar'        => $j,
                'aantal'      => $r->count(),
                'totaal'      => $r->sum('bedrag'),
                'overgemaakt' => $r->whereNotNull('overgemaakt_op')->sum('bedrag'),
                'open'        => $r->whereNull('overgemaakt_op')->sum('bedrag'),
            ];
        });

        return view('sponsorcommissie.index', compact('jaar', 'jaren', 'regels', 'overzicht'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'member_id'      => 'required|exists:members,id',
            'jaar'           => 'required|integer|min:2000|max:2100',
            'overgemaakt'    => 'required|boolean',
            'overgemaakt_op' => 'nullable|date',
        ]);

        $jaar     = (int) $data['jaar'];
        $memberId = (int) $data['member_id'];

        if (! $request->boolean('overgemaakt')) {
            SponsorcommissieAfdracht::where('member_id', $memberId)->where('jaar', $jaar)->delete();

            return back()->with('success', 'Markering "overgemaakt" verwijderd.');
        }

        $regel = $this->regelsVoorJaar($jaar)->first(fn ($r) => $r['member']->id === $memberId);
        if (! $regel) {
            return back()->with('error', "Dit lid heeft geen afdracht sponsorcommissie in {$jaar}.");
        }

        SponsorcommissieAfdracht::updateOrCreate(
            ['member_id' => $memberId, 'jaar' => $jaar],
            ['bedrag' => $regel['bedrag'], 'overgemaakt_op' => $data['overgemaakt_op'] ?? now()->toDateString()],
        );

        return back()->with('success', "Afdracht van {$regel['member']->full_name} gemarkeerd als overgemaakt.");
    }

    public function markeerAlles(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'jaar'           => 'required|integer|min:2000|max:2100',
            'overgemaakt_op' => 'required|date',
        ]);

        $jaar = (int) $data['jaar'];
        $open = $this->regelsVoorJaar($jaar)->whereNull('overgemaakt_op');

        foreach ($open as $regel) {
            SponsorcommissieAfdracht::create([
                'member_id'      => $regel['member']->id,
                'jaar'           => $jaar,
                'bedrag'         => $regel['bedrag'],
                'overgemaakt_op' => $data['overgemaakt_op'],
            ]);
        }

        return back()->with('success', "{$open->count()} afdracht(en) over {$jaar} gemarkeerd als overgemaakt.");
    }

    /**
     * Alle regels (lid, pakket, bedrag, overgemaakt_op) voor een jaar.
     *
     * Een lid telt mee als het lidmaatschap het jaar overlapt en het pakket een
     * afdracht heeft. Leden zonder einddatum tellen alleen mee als ze actief zijn.
     * Leden met een vastgelegde afdracht in dat jaar tellen altijd mee, met het
     * vastgelegde bedrag.
     */
    private function regelsVoorJaar(int $jaar): Collection
    {
        $begin = Carbon::create($jaar, 1, 1)->toDateString();
        $eind  = Carbon::create($jaar, 12, 31)->toDateString();

        $vastgelegd = SponsorcommissieAfdracht::with('member.membershipType')
            ->where('jaar', $jaar)
            ->get()
            ->keyBy('member_id');

        $leden = Member::with('membershipType')
            ->whereHas('membershipType', fn ($q) => $q->where('afdracht_sponsorcommissie', '>', 0))
            ->where(fn ($q) => $q->whereNull('membership_start')->orWhere('membership_start', '<=', $eind))
            ->where(fn ($q) => $q
                ->where(fn ($q) => $q->whereNotNull('membership_end')->where('membership_end', '>=', $begin))
                ->orWhere(fn ($q) => $q->whereNull('membership_end')->where('status', 'active')))
            ->get()
            ->keyBy('id');

        foreach ($vastgelegd as $memberId => $afdracht) {
            if (! $leden->has($memberId) && $afdracht->member) {
                $leden->put($memberId, $afdracht->member);
            }
        }

        return $leden
            ->map(function (Member $member) use ($vastgelegd) {
                $afdracht = $vastgelegd->get($member->id);

                return [
                    'member'         => $member,
                    'pakket'         => $member->membershipType?->name,
                    'bedrag'         => $afdracht
                        ? (float) $afdracht->bedrag
                        : (float) ($member->membershipType?->afdracht_sponsorcommissie ?? 0),
                    'overgemaakt_op' => $afdracht?->overgemaakt_op,
                ];
            })
            ->sortBy(fn ($r) => mb_strtolower($r['member']->last_name.' '.$r['member']->first_name))
            ->values();
    }

    /** Jaren vanaf de eerste lidmaatschapsstart of vastlegging t/m dit jaar (max. 15), nieuwste eerst. */
    private function jaren(): array
    {
        $huidig     = now()->year;
        $eersteLid  = Member::whereNotNull('membership_start')->min('membership_start');
        $kandidaten = array_filter([
            $huidig,
            $eersteLid ? Carbon::parse($eersteLid)->year : null,
            SponsorcommissieAfdracht::min('jaar'),
        ]);

        return range($huidig, max((int) min($kandidaten), $huidig - 15));
    }
}
