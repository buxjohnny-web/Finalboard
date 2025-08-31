<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Calculation;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
public function show($driver_id, $week)
{
    $driver = Driver::findOrFail($driver_id);

    // normalize week like CalculationController
    $weekNumber = is_numeric($week)
        ? (int) $week
        : (int) preg_replace('/\D+/', '', (string) $week);

    $calculation = Calculation::where('driver_id', $driver_id)
        ->where('week_number', $weekNumber)
        ->firstOrFail();

    return view('paydetails', compact('driver', 'calculation', 'week'));
}
}