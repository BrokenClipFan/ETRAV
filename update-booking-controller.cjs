const fs = require('fs');

let controllerPath = 'app/Http/Controllers/BookingController.php';
let text = fs.readFileSync(controllerPath, 'utf8');

let oldQuery = `$bookings = Booking::where('user_id', $user->id)
                     ->where('status', '!=', 'cancelled')`;

let newQuery = `$bookings = Booking::where('user_id', $user->id)
                     ->where(function($q) {
                         $q->where('status', '!=', 'cancelled')
                           ->orWhere(function($subQ) {
                               $subQ->where('status', 'cancelled')->where('notify', true);
                           });
                     })`;

text = text.replace(oldQuery, newQuery);
fs.writeFileSync(controllerPath, text);
console.log('done updating query');
