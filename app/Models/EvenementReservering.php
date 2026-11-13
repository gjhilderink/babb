<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Bedrag per lid dat in een jaar wordt gereserveerd voor evenementen.
 * Het totaal is bedrag_per_lid × het aantal actieve leden in dat jaar.
 */
class EvenementReservering extends Model
{
    protected $table = 'evenement_reserveringen';

    protected $fillable = [
        'jaar',
        'bedrag_per_lid',
        'omschrijving',
    ];

    protected $casts = [
        'jaar'           => 'integer',
        'bedrag_per_lid' => 'decimal:2',
    ];
}
