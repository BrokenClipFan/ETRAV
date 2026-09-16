const fs = require('fs');
let path = 'app/Http/Controllers/BookingController.php';
let text = fs.readFileSync(path, 'utf8');

// The line is: $status = $isCustom ? 'pending_price' : 'pending_downpayment';
let oldLine = `$status = $isCustom ? 'pending_price' : 'pending_downpayment';`;
let newLine = `$status = $isCustom ? 'pending_price' : 'pending';`;
text = text.replace(oldLine, newLine);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing backend booking status');
