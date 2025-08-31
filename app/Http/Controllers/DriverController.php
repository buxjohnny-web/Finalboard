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
            'full_name'      => ['required', 'string', 'max:255'],
            'phone_number'   => ['nullable', 'string', 'max:20'],
            'driver_id'      => ['required', 'string', 'max:50', 'unique:drivers,driver_id'],
            'license_number' => ['nullable', 'string', 'max:50'],
            'ssn'            => ['nullable', 'string', 'max:50'],
        ]);

        Driver::create([
            'full_name'      => $request->full_name,
            'phone_number'   => $request->phone_number,
            'driver_id'      => $request->driver_id,
            'license_number' => $request->license_number,
            'ssn'            => $request->ssn,
            'added_by'       => Auth::id(),
            'active'         => true,
        ]);

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
            'full_name'      => ['required', 'string', 'max:255'],
            'phone_number'   => ['nullable', 'string', 'max:20'],
            'driver_id'      => [
                'required',
                'string',
                'max:50',
                Rule::unique('drivers', 'driver_id')->ignore($driver->id),
            ],
            'license_number' => ['nullable', 'string', 'max:50'],
            'ssn'            => ['nullable', 'string', 'max:50'],
        ]);

        $driver->update($request->only([
            'full_name',
            'phone_number',
            'driver_id',
            'license_number',
            'ssn',
        ]));

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
}