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
                    // Store the file and extract the driver ID
                    $path = Storage::disk('public')->putFile('paystubs', $file);
                    $filename = $file->getClientOriginalName();
                    $parts = explode('-', pathinfo($filename, PATHINFO_FILENAME));
                    $rawDriverId = $parts[2] ?? $filename;
                    $driverId = preg_replace('/^C0/', '', $rawDriverId);

                    // Look for the driver in the database
                    $driver = Driver::where('driver_id', $driverId)->first();

                    if ($driver) {
                        // Get added_by user name if possible
                        $addedByUser = $driver->added_by ? \App\Models\User::find($driver->added_by) : null;
                        $uploaded[] = [
                            'filename' => $filename,
                            'driver_id' => $driver->driver_id,
                            'full_name' => $driver->full_name,
                            'phone_number' => $driver->phone_number,
                            'added_by_full_name' => $addedByUser ? $addedByUser->full_name : '',
                            'driver_id_db' => $driver->id,
                            'path' => $path,
                        ];
                    } else {
                        // Driver not found
                        $failed[] = [
                            'name' => $driverId, // just the ID minus the C0
                            'error' => 'Driver ID "' . $driverId . '" not found.'
                        ];
                    }
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