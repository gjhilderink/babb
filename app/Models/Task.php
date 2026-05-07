<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'assigned_to_user_id',
        'created_by',
        'meeting_id',
        'due_date',
        'status',
        'priority',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function priorityColor(): string
    {
        return match ($this->priority) {
            'hoog'   => 'bg-red-100 text-red-700',
            'normaal' => 'bg-yellow-100 text-yellow-700',
            default   => 'bg-gray-100 text-gray-500',
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'bezig'  => 'bg-blue-100 text-blue-700',
            'gereed' => 'bg-green-100 text-green-700',
            default  => 'bg-gray-100 text-gray-600',
        };
    }
}
