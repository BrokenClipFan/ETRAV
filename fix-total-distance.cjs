const fs = require('fs');
let path = 'app/Http/Controllers/BookingController.php';
let text = fs.readFileSync(path, 'utf8');

const regex = /\$pax = \(int\)\$validated\['number_of_heads'\];/;
text = text.replace(regex, `$pax = (int)$validated['number_of_heads'];\n        $totalDistance = (float)$validated['total_distance'];`);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing total distance variable');
