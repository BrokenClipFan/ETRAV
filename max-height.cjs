const fs = require('fs');
let path = 'resources/views/admin/bookings.blade.php';
let text = fs.readFileSync(path, 'utf8');

text = text.replace(
    /<div class="table-responsive bg-white rounded-4 shadow-sm border mb-4">/g,
    '<div class="table-responsive bg-white rounded-4 shadow-sm border mb-4" style="max-height: 400px; overflow-y: auto;">'
);

fs.writeFileSync(path, text, 'utf8');
console.log('Added max height');
