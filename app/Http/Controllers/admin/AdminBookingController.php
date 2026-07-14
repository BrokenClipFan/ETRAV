<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class AdminBookingController extends Controller
{
    public function index() {
        $bookings = Booking::with(['places', 'package', 'user'])->get();

        return view('admin.bookings', compact('bookings'));
    }

    public function update($id) {
        $booking = Booking::findOrFail($id);

        $booking->update([
            'status' => 'confirmed',
            'notify' => true
        ]);
        
        return redirect()->route('admin.bookings')->with('success', 'Booking Confirmed');
    }

    public function markComplete($id) {
        $booking = Booking::findOrFail($id);

        $booking->update([
            'status' => 'completed',
            'notify' => true
        ]);
        
        return redirect()->route('admin.bookings')->with('success', 'Booking set to Completed');
    }
}
