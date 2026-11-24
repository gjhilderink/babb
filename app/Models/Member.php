<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
        'factuur_referentie',
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
        'korting_type',
        'korting_waarde',
        'korting_reden',
        'korting_vanaf',
        'korting_tot',
    ];

    protected $casts = [
        'membership_start' => 'date',
        'membership_end'   => 'date',
        'korting_waarde'   => 'decimal:2',
        'korting_vanaf'    => 'date',
        'korting_tot'      => 'date',
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

    protected static function booted(): void
    {
        // Geen kortingstype = korting volledig wissen
        static::saving(function (self $member) {
            if (empty($member->korting_type)) {
                $member->korting_type   = null;
                $member->korting_waarde = null;
                $member->korting_reden  = null;
                $member->korting_vanaf  = null;
                $member->korting_tot    = null;
            }
        });
    }

    /** Leden met een korting ingesteld (ongeacht de periode) — zelfde regel als hasDiscount(). */
    public function scopeWithDiscount(Builder $query): Builder
    {
        return $query->whereIn('korting_type', ['percentage', 'bedrag'])->where('korting_waarde', '>', 0);
    }

    /** Leden zonder korting. */
    public function scopeWithoutDiscount(Builder $query): Builder
    {
        return $query->where(fn ($q) => $q->whereNull('korting_type')
            ->orWhereNotIn('korting_type', ['percentage', 'bedrag'])
            ->orWhereNull('korting_waarde')
            ->orWhere('korting_waarde', '<=', 0));
    }

    /** Leden met een korting die op de opgegeven datum geldt — zelfde regel als hasActiveDiscount(). */
    public function scopeWithActiveDiscount(Builder $query, $date = null): Builder
    {
        $date = Carbon::parse($date ?? now())->toDateString();

        return $query->withDiscount()
            ->where(fn ($q) => $q->whereNull('korting_vanaf')->orWhereDate('korting_vanaf', '<=', $date))
            ->where(fn ($q) => $q->whereNull('korting_tot')->orWhereDate('korting_tot', '>=', $date));
    }

    /** Is er een korting ingesteld (ongeacht de periode)? */
    public function hasDiscount(): bool
    {
        return in_array($this->korting_type, ['percentage', 'bedrag'], true)
            && (float) $this->korting_waarde > 0;
    }

    /** Geldt de korting op de opgegeven (factuur)datum? */
    public function hasActiveDiscount($date = null): bool
    {
        if (! $this->hasDiscount()) {
            return false;
        }

        $date = Carbon::parse($date ?? now())->startOfDay();

        if ($this->korting_vanaf && $date->lt($this->korting_vanaf->copy()->startOfDay())) {
            return false;
        }
        if ($this->korting_tot && $date->gt($this->korting_tot->copy()->startOfDay())) {
            return false;
        }

        return true;
    }

    /** Leesbare waarde, bijv. "10%" of "€ 50,00". */
    public function getKortingLabelAttribute(): ?string
    {
        if (! $this->hasDiscount()) {
            return null;
        }

        if ($this->korting_type === 'percentage') {
            $pct = (float) $this->korting_waarde;
            return (floor($pct) == $pct ? (int) $pct : number_format($pct, 2, ',', '.')) . '%';
        }

        return '€ ' . number_format((float) $this->korting_waarde, 2, ',', '.');
    }

    /**
     * Bereken de kortingsregel(s) voor een set factuurregels.
     * Bedragen zijn excl. btw. Bij een percentage krijgt elk btw-tarief een eigen regel;
     * een vast bedrag wordt geboekt op het tarief met het hoogste subtotaal (en nooit hoger dan dat subtotaal).
     *
     * @param  array<int, array{quantity:mixed, unit_price:mixed, tax_rate:mixed}>  $items
     * @return array<int, array<string, mixed>>
     */
    public function discountItemsFor(array $items, $date = null): array
    {
        if (! $this->hasActiveDiscount($date)) {
            return [];
        }

        $perRate = [];
        foreach ($items as $item) {
            $line = round((float) $item['quantity'] * (float) $item['unit_price'], 2);
            if ($line <= 0) {
                continue;
            }
            $rate = (string) (float) $item['tax_rate'];
            $perRate[$rate] = ($perRate[$rate] ?? 0) + $line;
        }

        if (! $perRate) {
            return [];
        }

        $label = 'Korting ' . $this->korting_label . ($this->korting_reden ? ' - ' . $this->korting_reden : '');
        $label = mb_substr($label, 0, 255);
        $lines = [];

        if ($this->korting_type === 'percentage') {
            $pct = min(100, (float) $this->korting_waarde);
            foreach ($perRate as $rate => $subtotal) {
                $amount = round($subtotal * $pct / 100, 2);
                if ($amount > 0) {
                    $lines[] = $this->discountLine($label, $amount, (float) $rate);
                }
            }
        } else {
            arsort($perRate);
            $rate   = array_key_first($perRate);
            $amount = round(min((float) $this->korting_waarde, $perRate[$rate]), 2);
            if ($amount > 0) {
                $lines[] = $this->discountLine($label, $amount, (float) $rate);
            }
        }

        return $lines;
    }

    private function discountLine(string $label, float $amount, float $rate): array
    {
        return [
            'product_id'  => null,
            'description' => $label,
            'quantity'    => 1,
            'unit_price'  => -$amount,
            'tax_rate'    => $rate,
        ];
    }
}
