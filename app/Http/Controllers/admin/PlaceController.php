<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Place;

class PlaceController extends Controller
{
    public function store(Request $request) {
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'entrance_fee' => 'required|numeric',
            'description' => 'required|string|max:255',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('packages/places', 'public');

            $validated['image_path'] = asset('storage/' . $path);
        }
        
        $validated['price'] = $validated['entrance_fee'];

        Place::create($validated);

        return redirect()->route('admin.packages')->with('success', 'New Spot has been saved');
    }
}
