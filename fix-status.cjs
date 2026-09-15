const fs = require('fs');
let controllerPath = 'app/Http/Controllers/Admin/AdminBookingController.php';
let text = fs.readFileSync(controllerPath, 'utf8');

text = text.replace(/'status' => 'available'/, "'status' => 'active'");

fs.writeFileSync(controllerPath, text);
console.log('done');
