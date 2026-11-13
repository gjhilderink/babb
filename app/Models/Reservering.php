<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Bedrag dat de club per jaar reserveert voor eigen besteding.
 * Wordt in mindering gebracht op de afdracht aan Bonboys over dat jaar.
 */
class Reservering extends Model
{
    protected $table = 'reserveringen';

    protected $fillable = [
        'jaar',
        'bedrag',
        'omschrijving',
    ];

    protected $casts = [
        'jaar'   => 'integer',
        'bedrag' => 'decimal:2',
    ];
}
