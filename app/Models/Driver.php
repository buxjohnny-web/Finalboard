<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name',
        'phone_number',
        'driver_id',
        'license_number',
        'ssn',
        'added_by',
        'active',
    ];

    /**
     * Get the user who added the driver.
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
    /**
 * Get the calculations for the driver.
 */
public function calculations()
{
    return $this->hasMany(Calculation::class);
}
}