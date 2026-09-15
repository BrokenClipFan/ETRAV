const fs = require('fs');
let text = fs.readFileSync('resources/views/bookings.blade.php', 'utf8');

text = text.replace(/class="card shadow-sm rounded-4 booking-card/g, 'class="card mb-4 shadow-sm rounded-4 booking-card');

fs.writeFileSync('resources/views/bookings.blade.php', text);
console.log('Done fixing margins');
