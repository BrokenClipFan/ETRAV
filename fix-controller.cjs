const fs = require('fs');
let path = 'app/Http/Controllers/Admin/AdminBookingController.php';
let text = fs.readFileSync(path, 'utf8');

text = text.replace(
    /orderByRaw\("CASE WHEN status = 'pending' THEN 0 ELSE 1 END"\)/g,
    "orderByRaw(\"CASE WHEN status IN ('pending', 'pending_price') THEN 0 ELSE 1 END\")"
);

fs.writeFileSync(path, text, 'utf8');
console.log('Fixed controller order');
