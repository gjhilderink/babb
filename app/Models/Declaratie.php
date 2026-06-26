<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Declaratie extends Model
{
    protected $table = 'declaraties';

    protected $fillable = [
        'omschrijving', 'bedrag', 'datum', 'categorie',
        'bijlage_path', 'bijlage_naam', 'status', 'notities', 'created_by',
    ];

    protected $casts = [
        'bedrag' => 'decimal:2',
        'datum'  => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'ingediend'   => 'Ingediend',
            'goedgekeurd' => 'Goedgekeurd',
            'betaald'     => 'Betaald',
            'afgewezen'   => 'Afgewezen',
            default       => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'ingediend'   => 'bg-gray-100 text-gray-600',
            'goedgekeurd' => 'bg-blue-100 text-blue-700',
            'betaald'     => 'bg-green-100 text-green-700',
            'afgewezen'   => 'bg-red-100 text-red-600',
            default       => 'bg-gray-100 text-gray-600',
        };
    }

    public function hasBijlage(): bool
    {
        return (bool) $this->bijlage_path;
    }
}
