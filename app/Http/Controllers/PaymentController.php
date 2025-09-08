<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Calculation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index()
    {
        $drivers = Driver::all();
        return view('payments', compact('drivers'));
    }

    public function batchUpload(Request $request)
{
    try {
        $files = $request->file('files');
        if (!$files) {
            $files = [];
        } elseif (!is_array($files)) {
            $files = [$files];
        }

        $request->validate([
            'files'   => 'required|array',
            'files.*' => 'required|file|mimes:pdf|max:5120',
        ]);

        $uploaded = [];
        $failed = [];

        foreach ($files as $file) {
            if (!$file || !$file->isValid()) {
                $failed[] = [
                    'name' => $file ? $file->getClientOriginalName() : 'unknown',
                    'error' => 'File is not valid or missing.'
                ];
                continue;
            }
            try {
                $path = Storage::disk('public')->putFile('paystubs', $file);

                // Extract Driver ID and remove leading C0
                $filename = $file->getClientOriginalName();
                $parts = explode('-', pathinfo($filename, PATHINFO_FILENAME));
                $driverId = $parts[2] ?? $filename;
                $driverId = preg_replace('/^C0/', '', $driverId);

                $uploaded[] = [
                    'filename' => $filename,
                    'driver_id' => $driverId,
                ];
            } catch (\Exception $e) {
                $failed[] = [
                    'name' => $file->getClientOriginalName(),
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success'      => true,
            'uploaded'     => $uploaded,
            'failed'       => $failed,
            'count'        => count($uploaded),
            'failed_count' => count($failed),
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $e->errors(),
        ], 422);
    }
}

    public function show($driver_id, $week)
    {
        $driver = Driver::findOrFail($driver_id);

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