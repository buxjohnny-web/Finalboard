<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'week_number',
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
        'total_invoice'         => 'decimal:2',
        'vehicule_rental_price' => 'decimal:2',
        'broker_percentage'     => 'decimal:2',
        'bonus'                 => 'decimal:2',
        'cash_advance'          => 'decimal:2',
        'final_amount'          => 'decimal:2',
        'parcel_rows_count'     => 'integer',
        'week_number'           => 'integer',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function logs()
    {
        return $this->hasMany(CalculationLog::class);
    }

    public function scopeForDriverAndWeek($query, int $driverId, int $weekNumber)
    {
        return $query->where('driver_id', $driverId)->where('week_number', $weekNumber);
    }
}