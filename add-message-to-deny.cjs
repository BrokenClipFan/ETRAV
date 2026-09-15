const fs = require('fs');

let controllerPath = 'app/Http/Controllers/Admin/AdminBookingController.php';
let text = fs.readFileSync(controllerPath, 'utf8');

let newDeny = `    public function deny(Request $request, $id) {
        $request->validate([
            'admin_message' => 'required|string|max:1000'
        ]);

        $booking = Booking::findOrFail($id);

        $booking->update([
            'status' => 'cancelled',
            'notify' => true,
            'admin_message' => $request->admin_message
        ]);

        // Just to be safe, if vehicle was assigned, make it active
        if ($booking->vehicle) {
            $booking->vehicle->update(['status' => 'active']);
        }
        
        return redirect()->route('admin.bookings')->with('success', 'Booking Denied. Reason sent to user.');
    }`;

text = text.replace(/    public function deny\(\$id\) \{\s*\$booking = Booking::findOrFail\(\$id\);\s*\$booking->update\(\[\s*'status' => 'cancelled',\s*'notify' => true\s*\]\);\s*\/\/ Just to be safe, if vehicle was assigned, make it available\s*if \(\$booking->vehicle\) \{\s*\$booking->vehicle->update\(\['status' => 'active'\]\);\s*\}\s*return redirect\(\)->route\('admin\.bookings'\)->with\('success', 'Booking Denied\.'\);\s*\}/, newDeny);

fs.writeFileSync(controllerPath, text);
console.log('done updating controller');
