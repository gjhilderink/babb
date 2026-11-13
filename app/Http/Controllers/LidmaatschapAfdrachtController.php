<?php

namespace App\Http\Controllers;

use App\Models\AfdrachtBestemming;
use App\Models\LidmaatschapAfdracht;
use App\Services\LidmaatschapAfdrachtService;
use App\Support\Seizoen;
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

    public function sponsorcommissie(): View
    {
        return view('sponsorcommissie.index', ['overzicht' => $this->service->overzichtPerJaar('sponsorcommissie')]);
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

        return back()->with('success', "{$open->count()} afdracht(en) over seizoen ".Seizoen::label($jaar)." gemarkeerd als overgemaakt.");
    }

    /** Bestemming van de afdracht over een jaar opslaan; leeg = verwijderen. */
    public function bestemming(Request $request, string $soort): RedirectResponse
    {
        $data = $request->validate([
            'jaar'       => 'required|integer|min:2000|max:2100',
            'bestemming' => 'nullable|string|max:2000',
        ]);

        $sleutel = ['soort' => $soort, 'jaar' => (int) $data['jaar']];

        if (blank($data['bestemming'] ?? null)) {
            AfdrachtBestemming::where($sleutel)->delete();

            return back()->with('success', "Bestemming voor seizoen ".Seizoen::label((int) $data['jaar'])." verwijderd.");
        }

        AfdrachtBestemming::updateOrCreate($sleutel, ['bestemming' => trim($data['bestemming'])]);

        return back()->with('success', "Bestemming voor seizoen ".Seizoen::label((int) $data['jaar'])." opgeslagen.");
    }

    /** Alle "overgemaakt"-markeringen van een jaar verwijderen. */
    public function jaarOngedaan(Request $request, string $soort): RedirectResponse
    {
        $data = $request->validate(['jaar' => 'required|integer|min:2000|max:2100']);

        $aantal = LidmaatschapAfdracht::where(['soort' => $soort, 'jaar' => (int) $data['jaar']])->delete();

        return back()->with('success', "Markering overgemaakt verwijderd voor {$aantal} afdracht(en) over seizoen ".Seizoen::label((int) $data['jaar']).".");
    }
}
