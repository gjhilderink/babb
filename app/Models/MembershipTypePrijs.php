<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Prijs en/of afdracht sponsorcommissie van een pakket in een specifiek jaar.
 * Een leeg veld betekent: de standaardwaarde van het pakket geldt.
 */
class MembershipTypePrijs extends Model
{
    protected $table = 'membership_type_prijzen';

    protected $fillable = [
        'membership_type_id',
        'jaar',
        'prijs',
        'afdracht_sponsorcommissie',
    ];

    protected $casts = [
        'jaar'                      => 'integer',
        'prijs'                     => 'decimal:2',
        'afdracht_sponsorcommissie' => 'decimal:2',
    ];

    public function membershipType()
    {
        return $this->belongsTo(MembershipType::class);
    }
}
