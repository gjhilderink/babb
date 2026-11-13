<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Waar de afdracht (bijv. aan Bonboys) over een jaar voor bestemd is. */
class AfdrachtBestemming extends Model
{
    protected $table = 'afdracht_bestemmingen';

    protected $fillable = [
        'soort',
        'jaar',
        'bestemming',
    ];

    protected $casts = [
        'jaar' => 'integer',
    ];
}
