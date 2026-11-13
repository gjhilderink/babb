<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Vastlegging dat de afdracht sponsorcommissie van een lid over een jaar
 * is overgemaakt naar de club. Het bedrag wordt bij het markeren vastgezet,
 * zodat latere wijzigingen aan het pakket de historie niet veranderen.
 */
class SponsorcommissieAfdracht extends Model
{
    protected $table = 'sponsorcommissie_afdrachten';

    protected $fillable = [
        'member_id',
        'jaar',
        'bedrag',
        'overgemaakt_op',
    ];

    protected $casts = [
        'jaar'           => 'integer',
        'bedrag'         => 'decimal:2',
        'overgemaakt_op' => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class)->withTrashed();
    }
}
