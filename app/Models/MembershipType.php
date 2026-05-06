<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price_per_year',
        'max_members',
        'benefits',
        'is_active',
    ];

    protected $casts = [
        'price_per_year' => 'decimal:2',
        'benefits'       => 'array',
        'is_active'      => 'boolean',
    ];

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
