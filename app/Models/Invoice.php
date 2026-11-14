<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'member_id',
        'issue_date',
        'due_date',
        'status',
        'notes',
        'subtotal',
        'tax_amount',
        'total',
        'paid_at',
        'sent_at',
        'sent_to',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date'   => 'date',
        'paid_at'    => 'datetime',
        'sent_at'    => 'datetime',
        'subtotal'   => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total'      => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'sent' && $this->due_date->isPast();
    }

    /** E-mailadres waar de factuur heen gaat: factuur-e-mail van het lid, anders het gewone adres. */
    public function ontvangerEmail(): ?string
    {
        return $this->member?->factuur_email ?: $this->member?->email ?: null;
    }

    public function kanVerzonden(): bool
    {
        return $this->status !== 'cancelled' && $this->ontvangerEmail() !== null;
    }

    public function recalculateTotals(): void
    {
        $subtotal   = $this->items->sum(fn ($i) => $i->line_total);
        $taxAmount  = $this->items->sum(fn ($i) => $i->tax_amount);

        $this->update([
            'subtotal'   => $subtotal,
            'tax_amount' => $taxAmount,
            'total'      => $subtotal + $taxAmount,
        ]);
    }
}
