<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Place;

class PlaceController extends Controller
{
    public function store(Request $request) {
        
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'entrance_fee' => 'required|numeric',
            'description'  => 'nullable|string',
            'category'     => 'required|string|max:255',
            'place'        => 'nullable|string|max:255', // Changed from 'address' to 'place'
            'longitude'    => 'required|numeric',
            'latitude'     => 'required|numeric',
            'image'        => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('packages/places', 'public');
            $validated['image_path'] = asset('storage/' . $path);
        }
        
        $validated['price'] = $validated['entrance_fee'];

        Place::create($validated);

        return redirect()->route('admin.packages')->with('success', 'New Spot has been saved');
    }

    public function update(Request $request, $id)
    {
        $place = Place::findOrFail($id);

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'entrance_fee' => 'required|numeric',
            'description'  => 'nullable|string',
            'category'     => 'required|string|max:255',
            'place'        => 'nullable|string|max:255',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('packages/places', 'public');
            $validated['image_path'] = asset('storage/' . $path);
        }

        $validated['price'] = $validated['entrance_fee'];

        $place->update($validated);

        return redirect()->route('admin.packages')->with('success', 'Spot details updated successfully');
    }

    public function destroy($id)
    {
        $place = Place::findOrFail($id);
        $place->delete();

        return redirect()->route('admin.packages')->with('success', 'Spot deleted successfully.');
    }
}