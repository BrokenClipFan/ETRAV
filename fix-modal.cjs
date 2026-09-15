const fs = require('fs');
let viewPath = 'resources/views/bookings.blade.php';
let text = fs.readFileSync(viewPath, 'utf8');

text = text.replace(/onclick="openGcashModal\(\{\{ \$booking->id \}\}, '&#8369;\{\{ number_format\(\$booking->deposit_amount, 2\) \}\}'\)\)"/g, 
'onclick="openGcashModal({{ $booking->id }}, \'&#8369;{{ number_format($booking->deposit_amount, 2) }}\')"');

fs.writeFileSync(viewPath, text);
console.log('done fixing modal onclick syntax error');
