<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salary extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'user_id',
        'amount',
        'effective_from',
        'effective_to',
        'notes',
    ];
    protected function casts(): array
    {
        return [
            'effective_from' => 'date',
            'effective_to' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    // orm
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // function rentang part 2 3 4
    public function scopeCurrent($query)
    {
        return $query->where(function($q) {
            $q->whereNull('effective_to')
              ->orWhere('effective_to', '>=', now());
        })->latest('effective_from');
    }
    public function scopeForDate($query, $date)
    {
        return $query->where('effective_from', '<=', $date)
            ->where(function($q) use ($date) {
                $q->whereNull('effective_to')
                  ->orWhere('effective_to', '>=', $date);
            });
    }

    // ai bikin format rupiah
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}
