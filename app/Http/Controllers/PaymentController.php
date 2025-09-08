<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        // Validate the file
        $request->validate([
            'files' => 'required|file|mimes:pdf|max:5120',
        ]);

        $file = $request->file('files');
        $path = Storage::disk('public')->putFile('paystubs', $file);

        // Extract driver ID from filename
        $filename = $file->getClientOriginalName();
        $parts = explode('-', pathinfo($filename, PATHINFO_FILENAME));
        $rawDriverId = $parts[2] ?? $filename;

        // Remove "C0" prefix if it exists
        $driverId = strtoupper(trim(preg_replace('/^C0/', '', $rawDriverId))); // Normalize ID format

        // Lookup driver in the database
        $driver = Driver::where('driver_id', $driverId)->first();

        // Build the response
        $response = [
            'success' => true,
            'message' => 'File uploaded successfully.',
            'uploaded' => [
                'filename' => $filename,
                'driver_id' => $driverId,
            ],
        ];

        // Check if the driver exists in the database
        if (!$driver) {
            $response['warning'] = 'Driver ID "' . $driverId . '" not found.';
        } else {
            $response['uploaded']['driver'] = [
                'driver_id' => $driver->driver_id,
                'full_name' => $driver->full_name,
            ];
        }

        return response()->json($response);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An unexpected error occurred.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
}