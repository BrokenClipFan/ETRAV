const fs = require('fs');
let viewPath = 'resources/views/bookings.blade.php';
let text = fs.readFileSync(viewPath, 'utf8');

// Replace question marks followed by {{ number_format
text = text.replace(/\?\{\{ number_format/g, '&#8369;{{ number_format');
text = text.replace(/,\{\{ number_format/g, '&#8369;{{ number_format');
text = text.replace(/,\{\{/g, '&#8369;{{');
text = text.replace(/\?\{\{/g, '&#8369;{{');

// The GCash modal has: openGcashModal({{ $booking->id }}, '?{{
text = text.replace(/openGcashModal\(\{\{ \$booking->id \}\}, '\?\{\{/g, "openGcashModal({{ $booking->id }}, '&#8369;{{");
text = text.replace(/openGcashModal\(\{\{ \$booking->id \}\}, ',\{\{/g, "openGcashModal({{ $booking->id }}, '&#8369;{{");

fs.writeFileSync(viewPath, text);
console.log('done fixing pesos');
