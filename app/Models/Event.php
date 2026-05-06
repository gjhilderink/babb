<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'event_end',
        'location',
        'status',
        'max_attendees',
        'notes',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'event_end'  => 'datetime',
    ];

    public function tasks()
    {
        return $this->hasMany(EventTask::class)->orderBy('due_date')->orderBy('id');
    }

    public function costs()
    {
        return $this->hasMany(EventCost::class)->orderBy('id');
    }

    public function totalCosts(): float
    {
        return (float) $this->costs->sum('amount');
    }

    public function openTasksCount(): int
    {
        return $this->tasks->whereIn('status', ['open', 'bezig'])->count();
    }
}
