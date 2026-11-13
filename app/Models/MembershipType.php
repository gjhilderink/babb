<?php

namespace App\Models;

use App\Services\LidmaatschapAfdrachtService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price_per_year',
        'afdracht_sponsorcommissie',
        'afdracht_bonboys',
        'max_members',
        'benefits',
        'is_active',
    ];

    protected $casts = [
        'price_per_year' => 'decimal:2',
        'afdracht_sponsorcommissie' => 'decimal:2',
        'afdracht_bonboys' => 'decimal:2',
        'benefits'       => 'array',
        'is_active'      => 'boolean',
    ];

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function prijzen()
    {
        return $this->hasMany(MembershipTypePrijs::class)->orderByDesc('jaar');
    }

    /** Prijs per jaar in een bepaald jaar; zonder vastgelegde jaarprijs geldt de standaardprijs. */
    public function prijsVoorJaar(int $jaar): float
    {
        $jaarPrijs = $this->prijzen->firstWhere('jaar', $jaar)?->prijs;

        return (float) ($jaarPrijs ?? $this->price_per_year);
    }

    /**
     * Afdracht (sponsorcommissie of bonboys) in een bepaald jaar;
     * zonder vastgelegd jaarbedrag geldt het standaardbedrag.
     */
    public function afdrachtVoorJaar(int $jaar, string $soort = 'sponsorcommissie'): float
    {
        $kolom        = LidmaatschapAfdrachtService::kolom($soort);
        $jaarAfdracht = $this->prijzen->firstWhere('jaar', $jaar)?->{$kolom};

        return (float) ($jaarAfdracht ?? $this->{$kolom});
    }
}
