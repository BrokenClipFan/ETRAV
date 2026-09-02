<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class AdminBookingController extends Controller
{
    public function index() {
        $bookings = Booking::with(['user', 'package', 'itinerary.place'])->latest()->get();
        return view('admin.bookings', compact('bookings'));
    }

    public function show($id) {
        $booking = Booking::with(['user', 'package', 'itinerary.place', 'vehicle'])->findOrFail($id);
        return view('admin.booking-details', compact('booking'));
    }

    public function update($id) {
        $booking = Booking::findOrFail($id);

        $booking->update([
            'status' => 'approved',
            'notify' => true
        ]);
        
        return redirect()->route('admin.bookings')->with('success', 'Booking Approved (Awaiting Customer Payment)');
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
