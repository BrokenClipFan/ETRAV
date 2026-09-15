const fs = require('fs');
let text = fs.readFileSync('resources/views/bookings.blade.php', 'utf8');

text = text.replace(/<div class="d-flex flex-wrap gap-2 justify-content-between w-100">\s*<div>\s*@if\(in_array\(\$booking->status, \['pending', 'approved', 'confirmed'\]\)\)\s*<button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-medium shadow-sm" onclick="confirmCancel\(\{\{ \$booking->id \}\}, '\{\{ \$booking->status \}\}'\)">Cancel Booking<\/button>\s*@endif\s*<\/div>\s*<div class="d-flex flex-wrap gap-2">([\s\S]*?)<\/div>\s*<\/div>/g, (match, p1) => {
    return `<div class="d-flex gap-2 justify-content-end w-100">
    ${p1.trim()}
    @if(in_array($booking->status, ['pending', 'approved', 'confirmed']))
        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-medium shadow-sm" onclick="confirmCancel({{ $booking->id }}, '{{ $booking->status }}')">Cancel</button>
    @endif
</div>`;
});

fs.writeFileSync('resources/views/bookings.blade.php', text);
console.log("Reverted");
