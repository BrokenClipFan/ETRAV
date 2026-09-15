const fs = require('fs');

// 1. AdminBookingController
let adminPath = 'app/Http/Controllers/Admin/AdminBookingController.php';
let adminText = fs.readFileSync(adminPath, 'utf8');
adminText = adminText.replace(/'status' => 'cancelled'/, "'status' => 'denied'");
fs.writeFileSync(adminPath, adminText);

// 2. BookingController
let userPath = 'app/Http/Controllers/BookingController.php';
let userText = fs.readFileSync(userPath, 'utf8');
userText = userText.replace(/->where\(function\(\$q\) \{ \$q->where\('status', '!=', 'cancelled'\)->orWhereNotNull\('admin_message'\); \}\)/, "->where('status', '!=', 'cancelled')");
fs.writeFileSync(userPath, userText);

console.log('done fixing controllers');
