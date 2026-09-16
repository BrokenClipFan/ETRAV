const fs = require('fs');
let path = 'resources/views/admin/bookings.blade.php';
let text = fs.readFileSync(path, 'utf8');

text = text.replace(
    /<td class="px-4 py-3 fw-medium text-dark">\{\{ \$booking->package->name \?\? 'Custom Package Bundle' \}\}<\/td>/g,
    `<td class="px-4 py-3 fw-medium text-dark">
        @if($booking->is_custom)
            <span class="badge bg-secondary me-1"><i class="bi bi-tools"></i> Custom</span>
        @endif
        {{ $booking->package->name ?? 'Custom Package Bundle' }}
    </td>`
);

fs.writeFileSync(path, text, 'utf8');
console.log('Fixed custom label');
