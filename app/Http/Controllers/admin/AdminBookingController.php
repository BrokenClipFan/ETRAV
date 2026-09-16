<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class AdminBookingController extends Controller
{
    public function index() {
        $bookings = Booking::with(['user', 'package', 'itinerary.place'])
            ->orderByRaw("CASE WHEN status IN ('pending', 'pending_price') THEN 0 ELSE 1 END")
            ->latest()
            ->get();
        return view('admin.bookings', compact('bookings'));
    }

    public function show($id) {
        $booking = Booking::with(['user', 'package', 'itinerary.place', 'vehicle'])->findOrFail($id);
        if ($booking->admin_notify) {
            $booking->update(['admin_notify' => false]);
        }
        return view('admin.booking-details', compact('booking'));
    }

    public function setPrice(Request $request, $id) {
        $request->validate([
            'quoted_price' => 'required|numeric|min:0'
        ]);
        $booking = Booking::findOrFail($id);
        $booking->update([
            'quoted_price' => $request->quoted_price,
            'total_price' => $request->quoted_price,
            'deposit_amount' => $request->quoted_price * 0.25,
            'status' => 'pending_downpayment',
            'notify' => true
        ]);
        return redirect()->back()->with('success', 'Custom price set successfully. Waiting for user payment.');
    }

    public function update($id) {
        $booking = Booking::findOrFail($id);

        $booking->update([
            'status' => 'approved',
            'notify' => true
        ]);

        
        
        return redirect()->route('admin.bookings')->with('success', 'Booking Approved (Awaiting Customer Payment)');
    }

    public function deny(Request $request, $id) {
        $request->validate([
            'admin_message' => 'required|string|max:1000'
        ]);

        $booking = Booking::findOrFail($id);

        $booking->update([
            'status' => 'denied',
            'notify' => true,
            'admin_message' => $request->admin_message
        ]);

        // Just to be safe, if vehicle was assigned, make it active
        if ($booking->vehicle) {
            $booking->vehicle->update(['status' => 'active']);
        }
        
        return redirect()->route('admin.bookings')->with('success', 'Booking Denied. Reason sent to user.');
    }

    public function markComplete($id) {
        $booking = Booking::findOrFail($id);

        $booking->update([
            'status' => 'completed',
            'notify' => true,
            'amount_paid' => $booking->total_price
        ]);

        
        
        return redirect()->route('admin.bookings')->with('success', 'Booking set to Completed (Balance Marked as Paid)');
    }

    public function calendar() {
        $todayBookings = Booking::with(['user', 'package', 'vehicle'])
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereDate('pickup_datetime', today())
            ->orderBy('pickup_datetime')
            ->get();
        return view('admin.calendar', compact('todayBookings'));
    }

    public function calendarEvents(\Illuminate\Http\Request $request) {
        $bookings = Booking::with(['user', 'package', 'vehicle'])
            ->whereIn('status', ['confirmed', 'completed'])
            ->get();
        
        $events = $bookings->map(function($b) {
            $title = ($b->package ? $b->package->name : 'Custom Package');
            $time = date('h:i A', strtotime($b->pickup_datetime));
            
            return [
                'id' => $b->id,
                'title' => $time . ' | ' . $title,
                'start' => date('Y-m-d', strtotime($b->pickup_datetime)),
                'allDay' => true,
                'color' => $b->status === 'completed' ? '#198754' : '#0d6efd',
                'extendedProps' => [
                    'vehicle' => $b->vehicle ? $b->vehicle->brand . ' ' . $b->vehicle->model : 'No Vehicle Selected',
                    'pax' => $b->pax,
                    'status' => ucfirst($b->status),
                    'client' => $b->user->name,
                    'time' => $time,
                    'booking_id' => $b->id
                ]
            ];
        });

        return response()->json($events);
    }

    public function statistics() {
        $totalBookings = Booking::count();
        $totalRevenue = Booking::whereIn('status', ['confirmed', 'completed'])->sum('amount_paid');
        
        $pendingCount = Booking::where('status', 'pending')->count();
        $approvedCount = Booking::where('status', 'approved')->count();
        $confirmedCount = Booking::where('status', 'confirmed')->count();
        $completedCount = Booking::where('status', 'completed')->count();

        $customBookingsCount = Booking::whereNull('package_id')->count();

        $recentBookings = Booking::with('user')->latest()->take(5)->get();
        $packages = \App\Models\Package::withCount('bookings')->orderByDesc('bookings_count')->take(5)->get();
        
        $popularPlaces = \App\Models\Place::withCount('bookingPlaces')
            ->orderByDesc('booking_places_count')
            ->take(5)
            ->get();

        return view('admin.statistics', compact(
            'totalBookings', 
            'totalRevenue', 
            'pendingCount', 
            'approvedCount',
            'confirmedCount', 
            'completedCount',
            'customBookingsCount',
            'recentBookings',
            'packages',
            'popularPlaces'
        ));
    }
}
