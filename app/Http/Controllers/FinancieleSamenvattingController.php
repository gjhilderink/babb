<?php

namespace App\Http\Controllers;

use App\Models\Afdracht;
use App\Models\Declaratie;
use App\Models\EventCost;
use App\Models\Invoice;
use App\Models\Reservering;
use App\Services\LidmaatschapAfdrachtService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Per jaar alle geldstromen: wat binnenkomt (facturen) en wat eruit gaat
 * (afdrachten en kosten). Alle bedragen excl. BTW, tenzij anders vermeld.
 */
class FinancieleSamenvattingController extends Controller
{
    public function __construct(private readonly LidmaatschapAfdrachtService $afdrachten) {}

    public function index(Request $request): View
    {
        $perJaar = $this->perJaar();
        $jaren   = $perJaar->keys()->all();
        $jaar    = in_array($request->integer('jaar'), $jaren, true) ? $request->integer('jaar') : now()->year;
        $detail  = $perJaar->get($jaar);

        return view('samenvatting.index', compact('perJaar', 'jaren', 'jaar', 'detail'));
    }

    /** @return Collection<int, array> per jaar (nieuwste eerst) */
    private function perJaar(): Collection
    {
        // Facturen: concepten en geannuleerde facturen tellen niet mee
        $facturen = Invoice::whereIn('status', ['sent', 'paid', 'overdue'])
            ->selectRaw('YEAR(issue_date) as jaar')
            ->selectRaw('SUM(subtotal) as gefactureerd')
            ->selectRaw("SUM(CASE WHEN status = 'paid' THEN subtotal ELSE 0 END) as ontvangen")
            ->selectRaw('SUM(tax_amount) as btw')
            ->groupBy('jaar')
            ->get()
            ->keyBy('jaar');

        $overigeAfdrachten = Afdracht::query()
            ->selectRaw('YEAR(COALESCE(datum, created_at)) as jaar')
            ->selectRaw('SUM(bedrag) as totaal')
            ->selectRaw("SUM(CASE WHEN status = 'betaald' THEN bedrag ELSE 0 END) as betaald")
            ->groupBy('jaar')
            ->get()
            ->keyBy('jaar');

        $declaraties = Declaratie::where('status', '!=', 'afgewezen')
            ->selectRaw('YEAR(datum) as jaar')
            ->selectRaw('SUM(bedrag) as totaal')
            ->selectRaw("SUM(CASE WHEN status = 'betaald' THEN bedrag ELSE 0 END) as betaald")
            ->groupBy('jaar')
            ->get()
            ->keyBy('jaar');

        // Evenementkosten tellen in het jaar van het evenement; betaald = er is een betaaldatum
        $evenementkosten = EventCost::query()
            ->join('events', 'events.id', '=', 'event_costs.event_id')
            ->whereNull('events.deleted_at')
            ->selectRaw('YEAR(events.event_date) as jaar')
            ->selectRaw('SUM(event_costs.amount) as totaal')
            ->selectRaw('SUM(CASE WHEN event_costs.paid_at IS NOT NULL THEN event_costs.amount ELSE 0 END) as betaald')
            ->groupBy('jaar')
            ->get()
            ->keyBy('jaar');

        $sponsorcommissie = $this->afdrachten->overzichtPerJaar('sponsorcommissie')->keyBy('jaar');
        $bonboys          = $this->afdrachten->overzichtPerJaar('bonboys')->keyBy('jaar');
        $reserveringen    = Reservering::pluck('bedrag', 'jaar');

        $huidig = now()->year;
        $alleJaren = collect([$huidig])
            ->merge($facturen->keys())->merge($overigeAfdrachten->keys())->merge($declaraties->keys())
            ->merge($evenementkosten->keys())->merge($sponsorcommissie->keys())->merge($bonboys->keys())
            ->merge($reserveringen->keys())
            ->map(fn ($j) => (int) $j)
            ->filter(fn ($j) => $j > 1999 && $j <= $huidig + 1)
            ->unique()
            ->sortDesc()
            ->values();

        $regel = fn (float $totaal, float $gerealiseerd) => [
            'totaal'       => $totaal,
            'gerealiseerd' => $gerealiseerd,
            'open'         => $totaal - $gerealiseerd,
        ];

        return $alleJaren->mapWithKeys(function (int $j) use ($facturen, $overigeAfdrachten, $declaraties, $evenementkosten, $sponsorcommissie, $bonboys, $reserveringen, $regel) {
            $f  = $facturen->get($j);
            $sc = $sponsorcommissie->get($j);
            $bb = $bonboys->get($j);
            $oa = $overigeAfdrachten->get($j);
            $de = $declaraties->get($j);
            $ev = $evenementkosten->get($j);

            $inkomsten = $regel((float) ($f->gefactureerd ?? 0), (float) ($f->ontvangen ?? 0));

            $afdrachten = [
                'Afdracht sponsorcommissie'       => $regel((float) ($sc['totaal'] ?? 0), (float) ($sc['overgemaakt'] ?? 0)),
                'Afdracht Bonboys (lidmaatschap)' => $regel((float) ($bb['af_te_dragen'] ?? 0), (float) ($bb['overgemaakt'] ?? 0)),
                'Overige afdrachten Bonboys'      => $regel((float) ($oa->totaal ?? 0), (float) ($oa->betaald ?? 0)),
            ];

            $kosten = [
                'Declaraties (overige kosten)' => $regel((float) ($de->totaal ?? 0), (float) ($de->betaald ?? 0)),
                'Evenementkosten'              => $regel((float) ($ev->totaal ?? 0), (float) ($ev->betaald ?? 0)),
            ];

            $totaalAfdrachten = collect($afdrachten)->sum('totaal');
            $totaalKosten     = collect($kosten)->sum('totaal');

            return [$j => [
                'jaar'              => $j,
                'inkomsten'         => $inkomsten,
                'btw'               => (float) ($f->btw ?? 0),
                'afdrachten'        => $afdrachten,
                'kosten'            => $kosten,
                'totaal_afdrachten' => $totaalAfdrachten,
                'totaal_kosten'     => $totaalKosten,
                'reservering'       => (float) $reserveringen->get($j, 0),
                'saldo'             => $inkomsten['totaal'] - $totaalAfdrachten - $totaalKosten,
            ]];
        });
    }
}
