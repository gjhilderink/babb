<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'description',
        'assigned_to',
        'status',
        'priority',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
