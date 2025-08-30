<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Driver;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    /**
     * Store a newly created driver in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'driver_id' => 'required|string|unique:drivers,driver_id|max:50',
            'license_number' => 'required|string|max:50',
            'ssn' => 'required|string|max:50',
        ]);

        Driver::create([
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'driver_id' => $request->driver_id,
            'license_number' => $request->license_number,
            'ssn' => $request->ssn,
            'added_by' => Auth::id(),
            'active' => true,
        ]);

        return redirect()->route('drivers')->with('success', 'Driver added successfully!');
    }

    /**
     * Display a listing of the drivers.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $drivers = \App\Models\Driver::all();
        return view('drivers', compact('drivers'));
    }

    /**
     * Show the form for editing the specified driver.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $driver = \App\Models\Driver::findOrFail($id);
        return view('editdriver', compact('driver'));
    }

    /**
     * Update the specified driver in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $driver = \App\Models\Driver::findOrFail($id);
        $driver->update($request->only(['full_name', 'phone_number', 'driver_id', 'license_number', 'ssn']));
        return redirect()->route('drivers')->with('success', 'Driver updated successfully!');
    }

    /**
     * Remove the specified driver from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $driver = \App\Models\Driver::findOrFail($id);
        $driver->delete();
        return redirect()->route('drivers')->with('success', 'Driver deleted successfully.');
    }

    /**
     * Display the specified driver.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $driver = \App\Models\Driver::findOrFail($id);
        return view('driver', compact('driver'));
    }
}
