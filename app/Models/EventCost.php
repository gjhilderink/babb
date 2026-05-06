<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'description',
        'amount',
        'category',
        'paid_by',
        'paid_at',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
