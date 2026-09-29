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
        $vehicles = Transport::where('status', 'active')->get();
        $bookedDates = \App\Models\Booking::whereNotNull('vehicle_id')
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('pickup_datetime', '>=', now()->startOfDay())
            ->get(['vehicle_id', 'pickup_datetime']);
        
        $hasNotification = Booking::where('user_id', $user->id)->where('notify', true)->exists();

        return view('welcome', compact('packages', 'places', 'user', 'vehicles', 'hasNotification', 'bookedDates'));
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
                       ->where('status', '!=', 'cancelled')
                       ->with(['itinerary.place', 'package']) 
                       ->orderByDesc('notify')
                       ->latest()
                       ->get();

        return view('bookings', compact('user', 'bookings'));
    }
    
    public function viewPackage(int $id) {
        $package = Package::with('places')->findOrFail($id);
        $packages = Package::all();
        $vehicles = Transport::where('status', 'active')->get();
        $bookedDates = \App\Models\Booking::whereNotNull('vehicle_id')
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('pickup_datetime', '>=', now()->startOfDay())
            ->get(['vehicle_id', 'pickup_datetime']);
        $places = Place::all();
        $categories = \App\Models\Category::all();
        $user = Auth::user();
        $hasNotification = Booking::where('user_id', $user->id)->where('notify', true)->exists();

        return view('view-package', compact('package', 'packages', 'vehicles', 'places', 'categories', 'user', 'hasNotification', 'bookedDates'));
    }

    public function viewCustomPackage() {
        $package = null;
        $packages = Package::all();
        $vehicles = Transport::where('status', 'active')->get();
        $bookedDates = \App\Models\Booking::whereNotNull('vehicle_id')
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('pickup_datetime', '>=', now()->startOfDay())
            ->get(['vehicle_id', 'pickup_datetime']);
        $places = Place::all();
        $categories = \App\Models\Category::all();
        $user = Auth::user();
        $hasNotification = Booking::where('user_id', $user->id)->where('notify', true)->exists();

        return view('view-package', compact('package', 'packages', 'vehicles', 'places', 'categories', 'user', 'hasNotification', 'bookedDates'));
    }

    public function editCustomPackage($id) {
        $editBooking = Booking::with('itinerary.place')->where('user_id', Auth::id())->findOrFail($id);
        
        $package = $editBooking->package_id ? Package::with('places')->find($editBooking->package_id) : null;
        $packages = Package::all();
        $vehicles = Transport::where('status', 'active')->get();
        $bookedDates = \App\Models\Booking::whereNotNull('vehicle_id')
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('pickup_datetime', '>=', now()->startOfDay())
            ->where('id', '!=', $id)
            ->get(['vehicle_id', 'pickup_datetime']);
        $places = Place::all();
        $categories = \App\Models\Category::all();
        $user = Auth::user();
        $hasNotification = Booking::where('user_id', $user->id)->where('notify', true)->exists();

        return view('view-package', compact('editBooking', 'package', 'packages', 'vehicles', 'places', 'categories', 'user', 'hasNotification', 'bookedDates'));
    }

    public function updateCustomBooking(Request $request, $id) {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);
        
        $validated = $request->validate([
            'package_id'       => 'nullable|exists:packages,id',
            'vehicle_id'       => 'required|exists:transports,id',
            'pickup_latitude'  => 'required|numeric|between:-90,90',
            'pickup_longitude' => 'required|numeric|between:-180,180',
            'pickup_place_name'=> 'required|string|max:255',
            'pickup_date'      => 'required|date',
            'pickup_time'      => 'required', 
            'number_of_heads'  => 'required|integer|min:1',
            'total_distance'   => 'required|numeric|min:0',
            'duration_hrs'     => 'nullable|array',
            'duration_mins'    => 'nullable|array',
        ]);

        $pickupDateTime = \Carbon\Carbon::parse($validated['pickup_date'] . ' ' . $validated['pickup_time']);
        
        \Illuminate\Support\Facades\DB::transaction(function () use ($booking, $validated, $pickupDateTime, $request) {
            $booking->update([
                'vehicle_id' => $validated['vehicle_id'],
                'pickup_datetime' => $pickupDateTime,
                'latitude' => $validated['pickup_latitude'],
                'longitude' => $validated['pickup_longitude'],
                'pickup_place_name' => $validated['pickup_place_name'],
                'pax' => $validated['number_of_heads'],
                'distance' => $validated['total_distance'],
                'is_custom' => true,
                'status' => 'pending_price',
                'total_price' => 0,
                'quoted_price' => null,
                'deposit_amount' => 0,
                'notify' => false,
                'admin_notify' => true
            ]);

            $booking->itinerary()->delete();

            $spotsOrder = $request->input('spots_order', array_keys($validated['duration_hrs'] ?? []));
            $customNames = $request->input('custom_spots_name', []);
            $customLats = $request->input('custom_spots_lat', []);
            $customLngs = $request->input('custom_spots_lng', []);
            $customCats = $request->input('custom_spots_category', []);

            foreach ($spotsOrder as $index => $placeId) {
                $hrs = (int)($validated['duration_hrs'][$placeId] ?? 0);
                $mins = (int)($validated['duration_mins'][$placeId] ?? 0);
                $totalMinutes = ($hrs * 60) + $mins;

                if (str_starts_with((string)$placeId, 'custom_')) {
                    $booking->itinerary()->create([
                        'place_id' => null,
                        'custom_name' => $customNames[$placeId] ?? 'Custom Stop',
                        'custom_latitude' => $customLats[$placeId] ?? 0,
                        'custom_longitude' => $customLngs[$placeId] ?? 0,
                        'custom_category' => $customCats[$placeId] ?? 'custom',
                        'duration_minutes' => $totalMinutes
                    ]);
                } else {
                    $booking->itinerary()->create([
                        'place_id' => $placeId,
                        'duration_minutes' => $totalMinutes
                    ]);
                }
            }
        });

        return redirect()->route('bookings.view')->with('success', 'Custom booking updated successfully! Waiting for admin to quote a new price.');
    }

    public function store(Request $request) {

        // 1. Validate the incoming request matching your exact payload keys
        $validated = $request->validate([
            'package_id'       => 'nullable|exists:packages,id',
            'vehicle_id'       => 'required|exists:transports,id',
            'pickup_latitude'  => 'required|numeric|between:-90,90',
            'pickup_longitude' => 'required|numeric|between:-180,180',
            'pickup_place_name'=> 'required|string|max:255',
            'pickup_date'      => 'required|date|after_or_equal:today',
            'pickup_time'      => 'required', 
            'number_of_heads'  => 'required|integer|min:1',
            'allow_joiners'    => 'nullable|boolean',
            'total_distance'   => 'required|numeric|min:0',

            // Validate the incoming duration associative arrays
            'duration_hrs'     => 'nullable|array',
            'duration_hrs.*'   => 'required|integer|min:0',
            'duration_mins'    => 'nullable|array',
            'duration_mins.*'  => 'required|integer|min:0|max:59',
        ]);

        // 2. Combine the Date and Time fields into a single carbon instance
        $pickupDateTime = Carbon::parse($validated['pickup_date'] . ' ' . $validated['pickup_time']);

        // 3. (Optional but recommended) Calculate the total and deposit on the backend!
        // Never trust prices/totals sent raw from the client-side.
        $vehicle = \App\Models\Transport::findOrFail($validated['vehicle_id']);
        
        $pax = (int)$validated['number_of_heads'];
        $totalDistance = (float)$validated['total_distance'];
        $baseVehiclePrice = (float)$vehicle->base_price;
        $intervalRate = (float)$vehicle->interval_rate;
        $pricingDistance = (float)$vehicle->pricing_distance;
        
        // 4. Check if the itinerary was customized (spots added or removed)
        $isCustomized = false;
        $spotsOrder = $request->input('spots_order', array_keys($validated['duration_hrs'] ?? []));
        $package = \App\Models\Package::with('places')->find($validated['package_id']);
        
        if ($package && count($spotsOrder) !== $package->places->count()) {
            $isCustomized = true;
        } else {
            foreach ($spotsOrder as $placeId) {
                if (str_starts_with((string)$placeId, 'custom_')) {
                    $isCustomized = true;
                    break;
                }
            }
        }
        
        // Check if it's a custom package (spots modified or no package selected)
        $isCustom = $isCustomized || empty($validated['package_id']) || $request->has('custom_spots_name');
        
        $packagePrice = $package ? (float)$package->package_price : 0;
        
        // Final Price Calculation
        $totalPrice = $isCustom ? 0 : $packagePrice;

        $validated['joiners'] = $request->has('allow_joiners');
        $capacity = (int)$vehicle->capacity > 0 ? (int)$vehicle->capacity : 1;

        $perHeadPrice = $totalPrice > 0 ? ($validated['joiners'] ? ($totalPrice / $capacity) : ($totalPrice / $pax)) : 0;
        
        // Downpayment logic
        $depositAmount = $isCustom ? 0 : ($totalPrice * 0.25);
        $status = $isCustom ? 'pending_price' : 'pending';
        
        $finalPackageId = $isCustomized ? null : $validated['package_id'];

        // 5. Create the Booking Record
        $booking = Booking::create([
            'user_id'         => Auth::id(), 
            'package_id'      => $finalPackageId,
            'vehicle_id'      => $validated['vehicle_id'],
            'pickup_datetime' => $pickupDateTime,
            'latitude'        => $validated['pickup_latitude'],
            'longitude'       => $validated['pickup_longitude'],
            'pickup_place_name' => $validated['pickup_place_name'],
            'pax'             => $pax,
            'distance'        => $totalDistance,
            'total_price'     => $totalPrice,
            'head_price'      => $perHeadPrice,
            'deposit_amount'  => $depositAmount,
            'joiners'         => $validated['joiners'],
            'status'          => $status,
            'is_custom'       => $isCustom, 
        ]);

        // 5. Structure and attach the stops to booking_places
        // Since we can have custom stops without a place_id, we'll insert them directly
        $bookingPlacesData = [];
        
        // We loop through the ordered spots array if we have one, otherwise fallback to duration_hrs keys
        $spotsOrder = $request->input('spots_order', array_keys($validated['duration_hrs'] ?? []));

        foreach ($spotsOrder as $placeId) {
            $hours = isset($validated['duration_hrs'][$placeId]) ? (int)$validated['duration_hrs'][$placeId] : 0;
            $minutes = isset($validated['duration_mins'][$placeId]) ? (int)$validated['duration_mins'][$placeId] : 0;
            $totalMinutes = ($hours * 60) + $minutes;

            if (str_starts_with((string)$placeId, 'custom_')) {
                // It's a custom stop
                $rawName = (string)$request->input("custom_spots_name.{$placeId}");
                $cleanName = str_replace(" Stop (Drag to adjust)", "", $rawName);

                $bookingPlacesData[] = [
                    'booking_id' => $booking->id,
                    'place_id'   => null,
                    'custom_name' => $cleanName,
                    'custom_latitude' => $request->input("custom_spots_lat.{$placeId}"),
                    'custom_longitude' => $request->input("custom_spots_lng.{$placeId}"),
                    'custom_category' => $request->input("custom_spots_category.{$placeId}"),
                    'duration_minutes' => $totalMinutes,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } else {
                // It's a standard DB place
                $bookingPlacesData[] = [
                    'booking_id' => $booking->id,
                    'place_id'   => $placeId,
                    'custom_name' => null,
                    'custom_latitude' => null,
                    'custom_longitude' => null,
                    'custom_category' => null,
                    'duration_minutes' => $totalMinutes,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert into the pivot table
        if (!empty($bookingPlacesData)) {
            \Illuminate\Support\Facades\DB::table('booking_places')->insert($bookingPlacesData);
        }

        return redirect()->route('bookings.view')->with('success', 'Booking created successfully!');
    }

    public function paymentPage($id) {
        $booking = Booking::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $user = Auth::user();
        return view('payment', compact('booking', 'user'));
    }

    public function payDeposit($id, Request $request) {
        $booking = Booking::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        $request->validate([
            'payment_method' => 'required|string',
            'reference_number' => 'required|string',
            'proof_of_payment' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('proof_of_payment')) {
            $path = $request->file('proof_of_payment')->store('payments', 'public');
            // Can be saved to booking model if it has a column
        }

        // For now, mark the booking as confirmed and record the 25% payment
        $booking->status = 'confirmed';
        $booking->amount_paid = $booking->deposit_amount;
        $booking->admin_notify = true;
        $booking->save();

        return redirect()->route('bookings.view')->with('success', 'Payment submitted successfully! Your booking is now confirmed.');
    }

    public function cancel($id) {
        $booking = Booking::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $booking->status = 'cancelled';
        $booking->notify = false;
        $booking->save();

        // Release the vehicle if it was tied up
        

        return redirect()->route('bookings.view')->with('success', 'Booking has been successfully cancelled.');
    }
}
