<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'week',
        'total_invoice',
        'parcel_rows_count',
        'vehicule_rental_price',
        'broker_percentage',
        'bonus',
        'cash_advance',
        'final_amount',
        'pdf_path',
    ];

    protected $casts = [
        'total_invoice' => 'decimal:2',
        'vehicule_rental_price' => 'decimal:2',
        'broker_percentage' => 'decimal:2',
        'bonus' => 'decimal:2',
        'cash_advance' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'parcel_rows_count' => 'integer',
        'week' => 'string',
    ];

    // Relationships
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function logs()
    {
        return $this->hasMany(CalculationLog::class);
    }

    // Scopes
    public function scopeForWeek($query, string $week)
    {
        return $query->where('week', $week);
    }

    public function scopeForDriver($query, int $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeForDriverAndWeek($query, int $driverId, string $week)
    {
        return $query->where('driver_id', $driverId)->where('week', $week);
    }
}