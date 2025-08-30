<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'calculation_id',
        'user_id',
        'total_invoice',
        'parcel_rows_count',
        'vehicule_rental_price',
        'broker_percentage',
        'bonus',
        'cash_advance',
        'final_amount',
        'pdf_path',
        'action',
    ];

    // Relationships
    public function calculation()
    {
        return $this->belongsTo(Calculation::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}