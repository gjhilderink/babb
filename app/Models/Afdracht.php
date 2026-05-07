<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Afdracht extends Model
{
    protected $table = 'afdrachten';

    protected $fillable = [
        'onderwerp', 'bedrag', 'status', 'datum', 'notities', 'created_by',
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
            'nieuw'          => 'Nieuw',
            'nog_te_betalen' => 'Nog te betalen',
            'betaald'        => 'Betaald',
            default          => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'nieuw'          => 'bg-gray-100 text-gray-600',
            'nog_te_betalen' => 'bg-orange-100 text-orange-700',
            'betaald'        => 'bg-green-100 text-green-700',
            default          => 'bg-gray-100 text-gray-600',
        };
    }
}
