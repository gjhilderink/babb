<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'sku',
        'price',
        'tax_rate',
        'category',
        'is_active',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'tax_rate'  => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function getPriceIncludingTaxAttribute(): float
    {
        return round($this->price * (1 + $this->tax_rate / 100), 2);
    }
}
