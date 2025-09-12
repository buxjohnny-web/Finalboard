<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    /**
     * Store a newly created driver in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name'            => ['required', 'string', 'max:255'],
            'phone_number'         => ['nullable', 'string', 'max:20'],
            'driver_id'            => ['required', 'string', 'max:50', 'unique:drivers,driver_id'],
            'license_number'       => ['nullable', 'string', 'max:50'],
            'ssn'                  => ['nullable', 'string', 'max:50'],
            'default_percentage'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'default_rental_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $data = [
            'full_name'            => $request->full_name,
            'phone_number'         => $request->phone_number,
            'driver_id'            => $request->driver_id,
            'license_number'       => $request->license_number,
            'ssn'                  => $request->ssn,
            'default_percentage'   => $request->filled('default_percentage') ? $request->input('default_percentage') : null,
            'default_rental_price' => $request->filled('default_rental_price') ? $request->input('default_rental_price') : null,
            'added_by'             => Auth::id(),
            'active'               => true,
        ];

        Driver::create($data);

        return redirect()->route('drivers')->with('success', __('messages.driver_added_success'));
    }

    /**
     * Display a listing of the drivers.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $drivers = Driver::all();
        return view('drivers', compact('drivers'));
    }

    /**
     * Show the form for editing the specified driver.
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $driver = Driver::findOrFail($id);
        return view('editdriver', compact('driver'));
    }

    /**
     * Update the specified driver in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);

        $request->validate([
            'full_name'            => ['required', 'string', 'max:255'],
            'phone_number'         => ['nullable', 'string', 'max:20'],
            'driver_id'            => [
                'required',
                'string',
                'max:50',
                Rule::unique('drivers', 'driver_id')->ignore($driver->id),
            ],
            'license_number'       => ['nullable', 'string', 'max:50'],
            'ssn'                  => ['nullable', 'string', 'max:50'],
            'default_percentage'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'default_rental_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $data = $request->only([
            'full_name',
            'phone_number',
            'driver_id',
            'license_number',
            'ssn',
        ]);

        // Ensure empty strings are saved as null for nullable decimals
        $data['default_percentage']   = $request->filled('default_percentage') ? $request->input('default_percentage') : null;
        $data['default_rental_price'] = $request->filled('default_rental_price') ? $request->input('default_rental_price') : null;

        $driver->update($data);

        return redirect()->route('drivers')->with('success', __('messages.driver_updated_success'));
    }

    /**
     * Remove the specified driver from storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->delete();

        return redirect()->route('drivers')->with('success', __('messages.driver_deleted_success'));
    }

    /**
     * Display the specified driver.
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $driver = Driver::findOrFail($id);
        return view('driver', compact('driver'));
    }

    /**
     * Toggle the active status of the specified driver.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleActive(Driver $driver, Request $request)
    {
        $driver->active = $request->boolean('active') ? 1 : 0;
        $driver->save();

        return response()->json(['success' => true, 'active' => $driver->active]);
    }
}