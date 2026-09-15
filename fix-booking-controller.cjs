const fs = require('fs');

let controllerPath = 'app/Http/Controllers/BookingController.php';
let text = fs.readFileSync(controllerPath, 'utf8');

text = text.replace(/->where\('status', '!=', 'cancelled'\)/, `->where(function($q) {
                         $q->where('status', '!=', 'cancelled')
                           ->orWhere(function($subQ) {
                               $subQ->where('status', 'cancelled')->where('notify', true);
                           });
                     })`);

fs.writeFileSync(controllerPath, text);
console.log('done fixing query');
