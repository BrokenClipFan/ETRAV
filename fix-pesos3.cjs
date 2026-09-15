const fs = require('fs');
let viewPath = 'resources/views/bookings.blade.php';
let text = fs.readFileSync(viewPath, 'utf8');

text = text.replace(/,/g, '&#8369;');
text = text.replace(/\?\{\{ number_format/g, '&#8369;{{ number_format');

fs.writeFileSync(viewPath, text);
console.log('done fixing pesos 3');
