<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser; // Composer: smalot/pdfparser

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
            $request->validate([
                'files' => 'required|file|mimes:pdf|max:5120',
            ]);

            $file = $request->file('files');
            $path = Storage::disk('public')->putFile('paystubs', $file);

            $filename = $file->getClientOriginalName();
            $parts = explode('-', pathinfo($filename, PATHINFO_FILENAME));
            $rawDriverId = $parts[2] ?? $filename;
            $driverId = strtoupper(trim(preg_replace('/^C0/', '', $rawDriverId)));

            $driver = Driver::where('driver_id', $driverId)->first();

            $parser = new Parser();
            $pdf = $parser->parseFile($file->getPathname());
            $pages = $pdf->getPages();
            $firstPageText = $pages[0]->getText();

            // Total invoice (keep logic; assumed red in PDF)
            if (preg_match('/Total invoice.*?\$?([\d,]+\.\d{2})/i', $firstPageText, $matches)) {
                $invoiceValue = $matches[1];
            } else {
                preg_match_all('/\$([\d,]+\.\d{2})/', $firstPageText, $allMatches);
                if (!empty($allMatches[1])) {
                    $maxValue = 0.0;
                    foreach ($allMatches[1] as $val) {
                        $num = floatval(str_replace(',', '', $val));
                        if ($num > $maxValue) $maxValue = $num;
                    }
                    $invoiceValue = number_format($maxValue, 2, '.', '');
                } else {
                    $invoiceValue = 'N/A';
                }
            }

            // Total parcels
            if (preg_match('/Total\s+\d+\s+([\d,]+)\s+\$[\d,]+\.\d{2}/i', $firstPageText, $matches)) {
                $parcelsQtyTotal = str_replace(',', '', $matches[1]);
            } elseif (preg_match('/Total\s+(\d{1,})\s+([\d,]{1,})/i', $firstPageText, $matches)) {
                $parcelsQtyTotal = str_replace(',', '', $matches[2]);
            } else {
                $parcelsQtyTotal = 'N/A';
            }

            // Days worked = count rows with parcels qty > 0
            $parcelsQtyCount = 0;
            if (preg_match_all('/(\d{4}-\d{2}-\d{2})\s+\d+\s+(\d+)\s+\$/', $firstPageText, $matches)) {
                foreach ($matches[2] as $qty) {
                    if (intval($qty) > 0) $parcelsQtyCount++;
                }
            }

            $response = [
                'success' => true,
                'uploaded' => [
                    'filename' => $filename,
                    'driver_id' => $driverId,
                    'invoice_value' => $invoiceValue,
                    'total_parcels' => $parcelsQtyTotal,
                    'days_worked' => $parcelsQtyCount,          // explicit key
                    'parcels_qty_total' => $parcelsQtyTotal,    // backward compat
                    'parcels_qty_count' => $parcelsQtyCount,    // backward compat
                ],
            ];

            if ($driver) {
                $response['uploaded']['driver'] = [
                    'driver_id' => $driver->driver_id,
                    'full_name' => $driver->full_name,
                ];
            } else {
                $response['warning'] = 'Driver ID "' . $driverId . '" not found.';
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