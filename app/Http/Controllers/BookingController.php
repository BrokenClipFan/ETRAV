<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Place;
use App\Models\Booking;
use App\Models\Transport;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class BookingController extends Controller
{
    public function index() {
        $packages = Package::with('places')->get();
        $places = Place::all();
        $user = Auth::user();
        $vehicles = Transport::all();
        
        $hasNotification = Booking::where('user_id', $user->id)->where('notify', true)->exists();

        return view('welcome', compact('packages', 'places', 'user', 'vehicles', 'hasNotification'));
    }

    public function turnOffNotification(int $id) 
    {
        $booking = Booking::findOrFail($id);

        $booking->update([
            'notify' => false
        ]);

        return redirect()->back()->with('status', 'Notifications turned off.');
    }

    public function view(){
        $user = Auth::user();

        $bookings = Booking::where('user_id', $user->id)
                       ->with(['places', 'package']) 
                       ->latest()
                       ->get();

        return view('bookings', compact('user', 'bookings'));
    }
    
    public function viewPackage(int $id) {
        $package = Package::with('places')->findOrFail($id);
        $packages = Package::all();
        $vehicles = Transport::where('status', 'active')->get();
        $places = Place::all();
        $user = Auth::user();
        $hasNotification = Booking::where('user_id', $user->id)->where('notify', true)->exists();

        return view('view-package', compact('package', 'packages', 'vehicles', 'places', 'user', 'hasNotification'));
    }

    public function store(Request $request) {

        // 1. Validate the incoming request matching your exact payload keys
        $validated = $request->validate([
            'package_id'       => 'required|exists:packages,id',
            'pickup_latitude'  => 'required|numeric|between:-90,90',
            'pickup_longitude' => 'required|numeric|between:-180,180',
            'pickup_place_name'=> 'required|string|max:255',
            'pickup_date'      => 'required|date|after_or_equal:today',
            'pickup_time'      => 'required', 
            'number_of_heads'  => 'required|integer|min:1',
            'allow_joiners'    => 'nullable|boolean',

            // Validate the incoming duration associative arrays
            'duration_hrs'     => 'required|array',
            'duration_hrs.*'   => 'required|integer|min:0',
            'duration_mins'    => 'required|array',
            'duration_mins.*'  => 'required|integer|min:0|max:59',
        ]);

        // 2. Combine the Date and Time fields into a single carbon instance
        $pickupDateTime = Carbon::parse($validated['pickup_date'] . ' ' . $validated['pickup_time']);

        // 3. (Optional but recommended) Calculate the total and deposit on the backend!
        // Never trust prices/totals sent raw from the client-side.
        $package = \App\Models\Package::findOrFail($validated['package_id']);
        
        $pax = (int)$validated['number_of_heads'];
        $basePrice = (float)$package->package_price;

        $validated['joiners'] = $request->has('allow_joiners');

        if($validated['joiners'])
            $perHeadPrice = (float)$package->pax / (float)$package->package_price;
        else
            $perHeadPrice = (float)$package->package_price / $pax;
        
        $depositAmount = $basePrice * 0.25; // 25% deposit

        // 4. Create the Booking Record
        
        $booking = Booking::create([
            'user_id'         => Auth::id(), 
            'package_id'      => $validated['package_id'],
            'pickup_datetime' => $pickupDateTime,
            'latitude'        => $validated['pickup_latitude'],
            'longitude'       => $validated['pickup_longitude'],
            'pickup_place_name' => $validated['pickup_place_name'],
            'pax'             => $pax,
            'total_price'     => $basePrice,
            'head_price'      => $perHeadPrice,
            'deposit_amount'  => $depositAmount,
            'joiners'         => $validated['joiners'],
            'status'          => 'pending', 
        ]);

        // 5. Structure and attach the custom stop durations to booking_places
        $itineraryData = [];
        
        // We loop through the keys of duration_hrs (which are the place IDs: 4, 5, etc.)
        foreach ($validated['duration_hrs'] as $placeId => $hours) {
            $minutes = isset($validated['duration_mins'][$placeId]) ? (int)$validated['duration_mins'][$placeId] : 0;
            
            $totalMinutes = ((int)$hours * 60) + (int)$minutes;

            $itineraryData[$placeId] = [
                'duration_minutes' => $totalMinutes,
            ];
        }

        // Attach to the belongsToMany relation
        $booking->places()->attach($itineraryData);

        return redirect()->route('home')->with('success', 'Booking saved successfully!');
    }
}
