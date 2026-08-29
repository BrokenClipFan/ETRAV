<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transport;
use Illuminate\Http\Request;

class TransportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicles = Transport::all();

        return view('admin.transport', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'plate_number' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'status' => 'required|string|max:255',
            'front_image' => 'required|image|mimes:jpeg,png,webp,avif,jpg',
            'side_image' => 'required|image|mimes:jpeg,png,webp,avif,jpg',
            'plate_image' => 'required|image|mimes:jpeg,png,webp,avif,jpg',
        ]);

        if(Transport::where('plate_number', $request->plate_number)->exists()){
            return back()->withErrors('error', 'plate number is already registered');
        }

        if($request->hasFile('front_image')) {
            $validated['front_image_path'] = $request->file('front_image')->store('vehicles/front', 'public');
        }
        if($request->hasFile('side_image')) {
            $validated['side_image_path'] = $request->file('side_image')->store('vehicles/side', 'public');
        }
        if($request->hasFile('plate_image')) {
            $validated['plate_image_path'] = $request->file('plate_image')->store('vehicles/plate', 'public');
        }

        Transport::create($validated);

        return back()->with('success', 'Vehicle Registered');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $vehicle = Transport::FindOrFail($id);

        $validated = $request->validate([
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer',
            'status' => 'nullable|string|max:255',
            'front_image' => 'nullable|image|mimes:jpeg,png,webp,avif,jpg',
            'side_image' => 'nullable|image|mimes:jpeg,png,webp,avif,jpg',
            'plate_image' => 'nullable|image|mimes:jpeg,png,webp,avif,jpg',
        ]);

        $validated = array_filter($validated, fn ($value) => !is_null($value));

        if($request->hasFile('front_image')) {
            $validated['front_image_path'] = $request->file('front_image')->store('vehicles/front', 'public');
        }
        if($request->hasFile('side_image')) {
            $validated['side_image_path'] = $request->file('side_image')->store('vehicles/side', 'public');
        }
        if($request->hasFile('plate_image')) {
            $validated['plate_image_path'] = $request->file('plate_image')->store('vehicles/plate', 'public');
        }

        $vehicle->update($validated);

        return back()->with('success', 'Vehicle Updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
