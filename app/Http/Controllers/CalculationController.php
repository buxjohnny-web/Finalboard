<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\Calculation;
use App\Models\CalculationLog;
use Smalot\PdfParser\Parser;

class CalculationController extends Controller
{
    public function show($driverId, $week)
    {
        $driver = Driver::findOrFail($driverId);
        $weekNumber = $this->toWeekNumber($week);

        // Ensure creation provides a default for non-nullable columns
        $calculation = Calculation::firstOrCreate(
            [
                'driver_id'   => $driver->id,
                'week_number' => $weekNumber,
            ],
            [
                'broker_percentage' => 0, // important default
            ]
        );

        return view('calculate', [
            'driver'      => $driver,
            'week'        => $week, // keep original for display
            'calculation' => $calculation,
        ]);
    }

    public function uploadPdf(Request $request)
    {
        $request->validate([
            'file'      => 'required|mimes:pdf|max:5120',
            'driver_id' => 'required|exists:drivers,id',
            'week'      => 'required',
        ]);

        $driverId   = (int) $request->driver_id;
        $weekNumber = $this->toWeekNumber($request->week);

        $pdfPath = $request->file('file')->store('pdfs', 'public');

        $text            = $this->extractTextFromPdf(storage_path('app/public/' . $pdfPath));
        $totalInvoice    = $this->extractTotalInvoice($text);
        $parcelRowsCount = $this->countParcelsInvoicedRows($text);

        // Ensure creation provides a default for non-nullable columns
        $calculation = Calculation::firstOrCreate(
            [
                'driver_id'   => $driverId,
                'week_number' => $weekNumber,
            ],
            [
                'broker_percentage' => 0, // important default
            ]
        );

        $old = $calculation->only([
            'total_invoice','parcel_rows_count','vehicule_rental_price','broker_percentage',
            'bonus','cash_advance','final_amount','pdf_path'
        ]);

        $calculation->update([
            'total_invoice'     => $totalInvoice,
            'parcel_rows_count' => $parcelRowsCount,
            'pdf_path'          => $pdfPath,
        ]);

        $new = $calculation->only([
            'total_invoice','parcel_rows_count','vehicule_rental_price','broker_percentage',
            'bonus','cash_advance','final_amount','pdf_path'
        ]);

        if ($old != $new) {
            $this->logCalculationChange($calculation, auth()->id(), $new, 'update');
        }

        return response()->json([
            'total_invoice'     => $totalInvoice,
            'parcel_rows_count' => $parcelRowsCount,
            'pdf_path'          => $pdfPath,
        ]);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'driver_id'             => 'required|exists:drivers,id',
            'week'                  => 'required',
            'vehicule_rental_price' => ['nullable','numeric','regex:/^\d+(\.\d{1,2})?$/'],
            'broker_percentage'     => ['required','numeric','between:0,100'],
            'bonus'                 => ['nullable','numeric','regex:/^\d+(\.\d{1,2})?$/'],
            'cash_advance'          => ['nullable','numeric','regex:/^\d+(\.\d{1,2})?$/'],
        ]);

        $weekNumber = $this->toWeekNumber($validated['week']);

        // Ensure creation provides a default for non-nullable columns
        $calculation = Calculation::firstOrCreate(
            [
                'driver_id'   => (int) $validated['driver_id'],
                'week_number' => $weekNumber,
            ],
            [
                'broker_percentage' => 0, // important default
            ]
        );

        $totalInvoice    = (float) ($calculation->total_invoice ?? 0);
        $parcelRowsCount = (int)   ($calculation->parcel_rows_count ?? 0);

        $vehiculeRental = (float) ($validated['vehicule_rental_price'] ?? 0);
        $percentage     = (float) $validated['broker_percentage'];
        $bonus          = (float) ($validated['bonus'] ?? 0);
        $cashAdvance    = (float) ($validated['cash_advance'] ?? 0);

        $driverShare = (100 - $percentage) / 100;
        $left        = $totalInvoice * $driverShare;
        $right       = $vehiculeRental * $parcelRowsCount;
        $finalAmount = round($left - $right + $bonus - $cashAdvance, 2);

        $old = $calculation->only(['vehicule_rental_price','broker_percentage','bonus','cash_advance','final_amount']);

        $calculation->update([
            'vehicule_rental_price' => $vehiculeRental,
            'broker_percentage'     => $percentage,
            'bonus'                 => $bonus,
            'cash_advance'          => $cashAdvance,
            'final_amount'          => $finalAmount,
        ]);

        $new = $calculation->only(['vehicule_rental_price','broker_percentage','bonus','cash_advance','final_amount']);

        if ($old != $new) {
            $this->logCalculationChange($calculation, auth()->id(), $calculation->only([
                'total_invoice','parcel_rows_count','vehicule_rental_price','broker_percentage','bonus','cash_advance','final_amount','pdf_path'
            ]), 'update');
        }

        return response()->json([
            'final_amount' => number_format($finalAmount, 2, '.', ''),
        ]);
    }

    protected function toWeekNumber($week): int
    {
        if (is_numeric($week)) return (int) $week;
        if (preg_match('/(\d{1,2})$/', (string) $week, $m)) return (int) $m[1];
        return (int) preg_replace('/\D+/', '', (string) $week) ?: 0;
    }

    protected function extractTextFromPdf(string $path): string
    {
        $parser = new Parser();
        $pdf    = $parser->parseFile($path);
        return $pdf->getText();
    }

    protected function extractTotalInvoice(string $text): ?float
    {
        if (preg_match('/Total\s+invoice\s*\$?\s*([0-9][0-9\.,]*)/i', $text, $m)) {
            return (float) str_replace(',', '', $m[1]);
        }
        return null;
    }

    protected function countParcelsInvoicedRows(string $text): int
    {
        preg_match_all('/^\d{4}-\d{2}-\d{2}\s+0\s+([1-9]\d*)/m', $text, $matches);
        return count($matches[1]);
    }

    protected function logCalculationChange(Calculation $calculation, ?int $userId, array $fields, string $action = 'update'): void
    {
        CalculationLog::create([
            'calculation_id'        => $calculation->id,
            'user_id'               => $userId,
            'total_invoice'         => $fields['total_invoice'] ?? null,
            'parcel_rows_count'     => $fields['parcel_rows_count'] ?? null,
            'vehicule_rental_price' => $fields['vehicule_rental_price'] ?? null,
            'broker_percentage'     => $fields['broker_percentage'] ?? 0,
            'bonus'                 => $fields['bonus'] ?? null,
            'cash_advance'          => $fields['cash_advance'] ?? null,
            'final_amount'          => $fields['final_amount'] ?? null,
            'pdf_path'              => $fields['pdf_path'] ?? null,
            'action'                => $action,
        ]);
    }
}