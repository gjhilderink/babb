<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'factuur_email',
        'phone',
        'company_name',
        'address',
        'postal_code',
        'city',
        'country',
        'prive_adres',
        'prive_postcode',
        'prive_stad',
        'membership_type_id',
        'membership_start',
        'membership_end',
        'status',
        'notes',
    ];

    protected $casts = [
        'membership_start' => 'date',
        'membership_end'   => 'date',
    ];

    public function membershipType()
    {
        return $this->belongsTo(MembershipType::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
