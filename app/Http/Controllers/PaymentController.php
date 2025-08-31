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

        // Normalize week similar to CalculationController::toWeekNumber
        if (is_numeric($week)) {
            $weekNumber = (int) $week;
        } elseif (preg_match('/(\d{1,2})$/', (string) $week, $m)) {
            $weekNumber = (int) $m[1];
        } else {
            $weekNumber = (int) preg_replace('/\D+/', '', (string) $week);
        }

        $calculation = Calculation::where('driver_id', $driver_id)
            ->where('week_number', $weekNumber)
            ->firstOrFail();

        return view('paydetails', compact('driver', 'calculation', 'week'));
    }
}