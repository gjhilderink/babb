<?php

namespace App\Services;

use App\Models\LidmaatschapAfdracht;
use App\Models\Member;
use App\Models\Reservering;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Berekent per jaar welke actieve leden welke afdracht verschuldigd zijn,
 * op basis van het pakket (met eventuele jaarprijzen) en de vastgelegde
 * "overgemaakt"-registraties.
 */
class LidmaatschapAfdrachtService
{
    /** Soort afdracht => kolom op membership_types / membership_type_prijzen. */
    public const SOORTEN = [
        'sponsorcommissie' => 'afdracht_sponsorcommissie',
        'bonboys'          => 'afdracht_bonboys',
    ];

    public static function kolom(string $soort): string
    {
        return self::SOORTEN[$soort] ?? throw new \InvalidArgumentException("Onbekende soort afdracht: {$soort}");
    }

    /**
     * Totalen en "overgemaakt"-status per jaar voor één soort afdracht, nieuwste jaar eerst.
     *
     * Bij Bonboys gaat de reservering van dat jaar eraf: af_te_dragen = totaal - reservering.
     * De reservering wordt ook afgetrokken van wat per lid als overgemaakt is vastgelegd,
     * zodat "overgemaakt" en "open" samen precies het af te dragen bedrag zijn.
     */
    public function overzichtPerJaar(string $soort): Collection
    {
        $reserveringen = $soort === 'bonboys'
            ? Reservering::pluck('bedrag', 'jaar')->map(fn ($b) => (float) $b)
            : collect();

        return collect($this->jaren($soort))->map(function (int $j) use ($soort, $reserveringen) {
            $r = $this->regelsVoorJaar($soort, $j);

            $totaal          = (float) $r->sum('bedrag');
            $reservering     = $reserveringen->get($j, 0.0);
            $afTeDragen      = max(0.0, $totaal - $reservering);
            $overgemaaktBruto = (float) $r->whereNotNull('overgemaakt_op')->sum('bedrag');
            $overgemaakt     = min($afTeDragen, max(0.0, $overgemaaktBruto - $reservering));

            return [
                'jaar'        => $j,
                'aantal'      => $r->count(),
                'totaal'      => $totaal,
                'reservering' => $reservering,
                'af_te_dragen' => $afTeDragen,
                'overgemaakt' => $overgemaakt,
                'open'        => $afTeDragen - $overgemaakt,
                'open_aantal' => $r->whereNull('overgemaakt_op')->count(),
                // Datum waarop het jaar (laatste lid) is overgemaakt
                'overgemaakt_op' => $r->pluck('overgemaakt_op')->filter()->max(),
            ];
        });
    }

    /**
     * Regels (lid, pakket, bedrag, overgemaakt_op) voor een jaar.
     *
     * Alleen actieve leden tellen mee: het lidmaatschap moet het jaar overlappen
     * en het pakket moet in dat jaar een afdracht van deze soort hebben. Actieve
     * leden met een vastgelegde afdracht in dat jaar tellen altijd mee, met het
     * vastgelegde bedrag.
     */
    public function regelsVoorJaar(string $soort, int $jaar): Collection
    {
        $kolom = self::kolom($soort);
        $begin = Carbon::create($jaar, 1, 1)->toDateString();
        $eind  = Carbon::create($jaar, 12, 31)->toDateString();

        $vastgelegd = LidmaatschapAfdracht::with('member.membershipType.prijzen')
            ->where('soort', $soort)
            ->where('jaar', $jaar)
            ->get()
            ->keyBy('member_id');

        // Pakket heeft een afdracht in dit jaar: via een jaarbedrag, of zonder jaarbedrag via het standaardbedrag
        $leden = Member::with('membershipType.prijzen')
            ->where('status', 'active')
            ->whereHas('membershipType', fn ($q) => $q->where(fn ($q) => $q
                ->whereHas('prijzen', fn ($p) => $p->where('jaar', $jaar)->where($kolom, '>', 0))
                ->orWhere(fn ($q) => $q
                    ->where($kolom, '>', 0)
                    ->whereDoesntHave('prijzen', fn ($p) => $p->where('jaar', $jaar)->whereNotNull($kolom)))))
            ->where(fn ($q) => $q->whereNull('membership_start')->orWhere('membership_start', '<=', $eind))
            ->where(fn ($q) => $q->whereNull('membership_end')->orWhere('membership_end', '>=', $begin))
            ->get()
            ->keyBy('id');

        foreach ($vastgelegd as $memberId => $afdracht) {
            $member = $afdracht->member;
            if (! $leden->has($memberId) && $member && ! $member->trashed() && $member->status === 'active') {
                $leden->put($memberId, $member);
            }
        }

        return $leden
            ->map(function (Member $member) use ($vastgelegd, $soort, $jaar) {
                $afdracht = $vastgelegd->get($member->id);

                return [
                    'member'         => $member,
                    'pakket'         => $member->membershipType?->name,
                    'bedrag'         => $afdracht
                        ? (float) $afdracht->bedrag
                        : ($member->membershipType?->afdrachtVoorJaar($jaar, $soort) ?? 0.0),
                    'overgemaakt_op' => $afdracht?->overgemaakt_op,
                ];
            })
            ->sortBy(fn ($r) => mb_strtolower($r['member']->last_name.' '.$r['member']->first_name))
            ->values();
    }

    /** Jaren vanaf de eerste lidmaatschapsstart of vastlegging t/m dit jaar (max. 15), nieuwste eerst. */
    public function jaren(string $soort): array
    {
        $huidig     = now()->year;
        $eersteLid  = Member::whereNotNull('membership_start')->min('membership_start');
        $kandidaten = array_filter([
            $huidig,
            $eersteLid ? Carbon::parse($eersteLid)->year : null,
            LidmaatschapAfdracht::where('soort', $soort)->min('jaar'),
            $soort === 'bonboys' ? Reservering::min('jaar') : null,
        ]);

        return range($huidig, max((int) min($kandidaten), $huidig - 15));
    }
}
