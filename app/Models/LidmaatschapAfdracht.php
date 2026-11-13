<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Vastlegging dat de afdracht (sponsorcommissie of Bonboys) van een lid over
 * een jaar is overgemaakt. Het bedrag wordt bij het markeren vastgezet, zodat
 * latere wijzigingen aan het pakket de historie niet veranderen.
 */
class LidmaatschapAfdracht extends Model
{
    protected $table = 'lidmaatschap_afdrachten';

    protected $fillable = [
        'member_id',
        'soort',
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
