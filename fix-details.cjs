const fs = require('fs');
let path = 'resources/views/admin/booking-details.blade.php';
let text = fs.readFileSync(path, 'utf8');

text = text.replace(
    /\{\{ \$booking->package->name \?\? 'Custom Itinerary' \}\}/g,
    `@if($booking->is_custom) <span class="badge bg-secondary me-1"><i class="bi bi-tools"></i> Custom Route</span> @endif {{ $booking->package->name ?? 'Custom Itinerary' }}`
);

fs.writeFileSync(path, text, 'utf8');
console.log('Fixed details view label');
