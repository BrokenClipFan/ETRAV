const fs = require('fs');
let text = fs.readFileSync('resources/views/bookings.blade.php', 'utf8');

text = text.replace(/<div class="d-flex gap-2">\s*<button class="btn btn-outline-secondary btn-sm[\s\S]*?(?:<\/button>\s*@endif|\s*<\/button>)\s*<\/div>/g, (match) => {
    let inner = match.replace(/<div class="d-flex gap-2">\s*/, '');
    inner = inner.replace(/\s*<\/div>$/, '');
    
    return `<div class="d-flex flex-wrap gap-2 justify-content-between w-100">
    <div>
        @if(in_array($booking->status, ['pending', 'approved', 'confirmed']))
            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-medium shadow-sm" onclick="confirmCancel({{ $booking->id }}, '{{ $booking->status }}')">Cancel Booking</button>
        @endif
    </div>
    <div class="d-flex flex-wrap gap-2">
        ${inner}
    </div>
</div>`;
});

// For the buttons that don't have `<div class="d-flex gap-2">` and are just floating... wait, the output showed:
text = text.replace(/<button class="btn btn-outline-secondary btn-sm rounded-pill"[\s\S]*?<\/button>/g, (match) => {
    return `<div class="d-flex flex-wrap gap-2 justify-content-between w-100">
    <div>
        @if(in_array($booking->status, ['pending', 'approved', 'confirmed']))
            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-medium shadow-sm" onclick="confirmCancel({{ $booking->id }}, '{{ $booking->status }}')">Cancel Booking</button>
        @endif
    </div>
    <div class="d-flex flex-wrap gap-2">
        ${match}
    </div>
</div>`;
});

fs.writeFileSync('resources/views/bookings.blade.php', text);
console.log("Done");
