const fs = require('fs');
let path = 'app/Http/Controllers/BookingController.php';
let text = fs.readFileSync(path, 'utf8');

// The line is: $totalPrice = $isCustom ? 0 : ($baseVehiclePrice + $packagePrice);
let oldLine = '$totalPrice = $isCustom ? 0 : ($baseVehiclePrice + $packagePrice);';
let newLine = '$totalPrice = $isCustom ? 0 : $packagePrice;';
text = text.replace(oldLine, newLine);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing backend booking controller');
