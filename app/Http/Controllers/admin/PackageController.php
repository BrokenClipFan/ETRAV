<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;

class PackageController extends Controller
{

    public function index() {
        $packages = Package::all();
        return view('admin.packages', compact('packages'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'package_price' => 'required|numeric',
            'perhead_price' => 'required|numeric',
            'pax' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'required|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('packages', 'public');

            $validated['image_path'] = asset('storage/' . $path);
        }


        Package::create($validated);

        return redirect()->route('admin.packages')->with('success', 'A new package has been created');
    }
}
