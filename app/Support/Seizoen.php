<?php

namespace App\Support;

use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Financieel jaar = voetbalseizoen: loopt van 1 september t/m 31 augustus.
 * Een seizoen wordt aangeduid met het startjaar: 2026 = seizoen 26/27
 * (1 september 2026 t/m 31 augustus 2027).
 */
final class Seizoen
{
    public const STARTMAAND = 9;

    /** Seizoen (startjaar) waarin een datum valt. */
    public static function vanDatum(CarbonInterface|string $datum): int
    {
        $d = $datum instanceof CarbonInterface ? $datum : Carbon::parse($datum);

        return $d->month >= self::STARTMAAND ? $d->year : $d->year - 1;
    }

    public static function huidig(): int
    {
        return self::vanDatum(now());
    }

    public static function begin(int $seizoen): Carbon
    {
        return Carbon::create($seizoen, self::STARTMAAND, 1)->startOfDay();
    }

    public static function eind(int $seizoen): Carbon
    {
        return self::begin($seizoen + 1)->subDay()->endOfDay();
    }

    /** [begin, eind] als datumstrings, voor whereBetween op een DATE-kolom. */
    public static function periode(int $seizoen): array
    {
        return [self::begin($seizoen)->toDateString(), self::eind($seizoen)->toDateString()];
    }

    /** [begin, eind] als datetime-strings, voor whereBetween op een DATETIME/TIMESTAMP-kolom. */
    public static function periodeTijd(int $seizoen): array
    {
        return [self::begin($seizoen)->toDateTimeString(), self::eind($seizoen)->toDateTimeString()];
    }

    /** Kort label: 2026 → "26/27". */
    public static function label(int $seizoen): string
    {
        return sprintf('%02d/%02d', $seizoen % 100, ($seizoen + 1) % 100);
    }

    /** Weergave voor koppen en lijsten: 2026 → "2026 (seizoen 26/27)". */
    public static function weergave(int $seizoen): string
    {
        return $seizoen.' (seizoen '.self::label($seizoen).')';
    }

    /** SQL-expressie (MySQL) die het seizoen van een datumkolom berekent. */
    public static function sql(string $kolom): string
    {
        return "(YEAR({$kolom}) - CASE WHEN MONTH({$kolom}) < ".self::STARTMAAND." THEN 1 ELSE 0 END)";
    }
}
