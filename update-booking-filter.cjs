const fs = require('fs');
let controllerPath = 'app/Http/Controllers/BookingController.php';
let text = fs.readFileSync(controllerPath, 'utf8');

text = text.replace(/->where\(function\(\$q\) \{\s*\$q->where\('status', '!=', 'cancelled'\)\s*->orWhere\(function\(\$subQ\) \{\s*\$subQ->where\('status', 'cancelled'\)->where\('notify', true\);\s*\}\);\s*\}\)/g, "->where(function($q) { $q->where('status', '!=', 'cancelled')->orWhereNotNull('admin_message'); })");

fs.writeFileSync(controllerPath, text);
console.log('done updating filter regex');
