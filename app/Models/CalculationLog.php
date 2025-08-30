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

    protected $casts = [
        'total_invoice'         => 'decimal:2',
        'vehicule_rental_price' => 'decimal:2',
        'broker_percentage'     => 'decimal:2',
        'bonus'                 => 'decimal:2',
        'cash_advance'          => 'decimal:2',
        'final_amount'          => 'decimal:2',
        'parcel_rows_count'     => 'integer',
    ];

    public function calculation()
    {
        return $this->belongsTo(Calculation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}