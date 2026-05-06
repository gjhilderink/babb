<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'company_name',
        'source', 'status', 'notes',
        'referred_by_member_id', 'referred_by_name',
        'assigned_to_user_id',
        'member_id', 'converted_at',
    ];

    protected $casts = [
        'converted_at' => 'datetime',
    ];

    public function referredByMember()
    {
        return $this->belongsTo(Member::class, 'referred_by_member_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function isConverted(): bool
    {
        return $this->converted_at !== null;
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'nieuw'      => 'Nieuw',
            'contact'    => 'In contact',
            'follow_up'  => 'Follow-up',
            'gewonnen'   => 'Gewonnen',
            'verloren'   => 'Verloren',
            default      => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'nieuw'     => 'bg-blue-100 text-blue-700',
            'contact'   => 'bg-yellow-100 text-yellow-700',
            'follow_up' => 'bg-orange-100 text-orange-700',
            'gewonnen'  => 'bg-green-100 text-green-700',
            'verloren'  => 'bg-red-100 text-red-600',
            default     => 'bg-gray-100 text-gray-600',
        };
    }
}
