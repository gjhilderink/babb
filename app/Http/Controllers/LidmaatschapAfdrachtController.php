<?php

namespace App\Http\Controllers;

use App\Models\LidmaatschapAfdracht;
use App\Services\LidmaatschapAfdrachtService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Afdrachten per lid op basis van het lidmaatschapspakket (sponsorcommissie en Bonboys).
 * De Bonboys-variant wordt getoond op de pagina "Afdracht Bonboys" (AfdrachtenController).
 */
class LidmaatschapAfdrachtController extends Controller
{
    public function __construct(private readonly LidmaatschapAfdrachtService $service) {}

    public function sponsorcommissie(Request $request): View
    {
        $data = $this->service->overzicht('sponsorcommissie', $request->integer('jaar') ?: null);

        return view('sponsorcommissie.index', $data);
    }

    public function update(Request $request, string $soort): RedirectResponse
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
            LidmaatschapAfdracht::where(['member_id' => $memberId, 'soort' => $soort, 'jaar' => $jaar])->delete();

            return back()->with('success', 'Markering "overgemaakt" verwijderd.');
        }

        $regel = $this->service->regelsVoorJaar($soort, $jaar)->first(fn ($r) => $r['member']->id === $memberId);
        if (! $regel) {
            return back()->with('error', "Dit lid heeft geen afdracht in {$jaar}.");
        }

        LidmaatschapAfdracht::updateOrCreate(
            ['member_id' => $memberId, 'soort' => $soort, 'jaar' => $jaar],
            ['bedrag' => $regel['bedrag'], 'overgemaakt_op' => $data['overgemaakt_op'] ?? now()->toDateString()],
        );

        return back()->with('success', "Afdracht van {$regel['member']->full_name} gemarkeerd als overgemaakt.");
    }

    public function markeerAlles(Request $request, string $soort): RedirectResponse
    {
        $data = $request->validate([
            'jaar'           => 'required|integer|min:2000|max:2100',
            'overgemaakt_op' => 'required|date',
        ]);

        $jaar = (int) $data['jaar'];
        $open = $this->service->regelsVoorJaar($soort, $jaar)->whereNull('overgemaakt_op');

        foreach ($open as $regel) {
            LidmaatschapAfdracht::create([
                'member_id'      => $regel['member']->id,
                'soort'          => $soort,
                'jaar'           => $jaar,
                'bedrag'         => $regel['bedrag'],
                'overgemaakt_op' => $data['overgemaakt_op'],
            ]);
        }

        return back()->with('success', "{$open->count()} afdracht(en) over {$jaar} gemarkeerd als overgemaakt.");
    }
}
