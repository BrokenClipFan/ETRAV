const fs = require('fs');

let file1 = 'resources/views/bookings.blade.php';
let text1 = fs.readFileSync(file1, 'utf8');
text1 = text1.replace(/status', 'cancelled'/g, "status', 'denied'");
text1 = text1.replace(/status === 'cancelled'/g, "status === 'denied'");
text1 = text1.replace(/if \(status === 'cancelled'\) deniedUnread = true;/g, "if (status === 'denied') deniedUnread = true;");
fs.writeFileSync(file1, text1);

let file2 = 'resources/views/admin/bookings.blade.php';
let text2 = fs.readFileSync(file2, 'utf8');
text2 = text2.replace(/data-filter="cancelled"/g, 'data-filter="denied"');
fs.writeFileSync(file2, text2);

let file3 = 'resources/views/admin/booking-details.blade.php';
let text3 = fs.readFileSync(file3, 'utf8');
text3 = text3.replace(/status === 'cancelled'/g, "status === 'denied'");
fs.writeFileSync(file3, text3);

console.log('done fixing views');
