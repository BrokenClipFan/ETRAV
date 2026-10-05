<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\PackagePlace;
use App\Models\Place;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function index() {
        $packages = Package::with('places')->get();
        $allRegisteredSpots = Place::all();
        $categories = \App\Models\Category::all();

        return view('admin.packages', compact('packages', 'allRegisteredSpots', 'categories'));
    }

    public function store(Request $request) {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'package_price' => 'required|numeric|min:0',
            'type' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'required|string|max:255',
            'attached_spot_ids' => 'required|string',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('packages', 'public');

            $validated['image_path'] = asset('storage/' . $path);
        }

        $package = Package::create($validated);

        $spots = json_decode($request->input('attached_spot_ids'), true);

        if (is_array($spots)) {
            $pivotData = [];

            foreach ($spots as $spot) {
                $placeId = $spot['id'];

                $pivotData[$placeId] = [
                    'position' => $spot['position'],
                    'duration' => $spot['duration'],
                ];
            }

            $package->places()->sync($pivotData);
        }

        return redirect()->route('admin.packages')->with('success', 'A new package has been created');
    }

    public function update(Request $request, int $id) {

        $package = Package::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'package_price' => 'required|numeric|min:0',
            'type' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'required|string|max:255',
            'attached_spot_ids' => 'required|string',
        ]);

        if ($request->hasFile('image')) {
    
            if (!empty($package->image_path)) {
                $relativeOldPath = Str::after($package->image_path, 'storage/');
                
                if (Storage::disk('public')->exists($relativeOldPath)) {
                    Storage::disk('public')->delete($relativeOldPath);
                }
            }
            
            // Store the newly uploaded file
            $path = $request->file('image')->store('packages', 'public');
            $validated['image_path'] = asset('storage/' . $path);
        }

        $package->update($validated);

        $spots = json_decode($request->input('attached_spot_ids'), true);

        if (is_array($spots)) {
            // Prepare an array formatted for Laravel's sync/attach pivot system
            // Format needed: [ place_id => ['pivot_column' => value] ]
            $pivotData = [];
            
            foreach ($spots as $spot) {
                $placeId = $spot['id'];

                $pivotData[$placeId] = [
                    'position' => $spot['position'],
                    'duration' => $spot['duration'],
                ];
            }

            // 5. Sync attaches the data to the package_places table automatically
            $package->places()->sync($pivotData);
        }

        return redirect()->route('admin.packages')->with('success', 'The package has been successfully updated');
    }

    public function destroy($id) {
        Package::destroy($id);

        return redirect()->route('admin.packages')->with('success', 'successfully deleted package');
    }
}
