const fs = require('fs');
let viewPath = 'resources/views/bookings.blade.php';
let text = fs.readFileSync(viewPath, 'utf8');

text = text.replace(/let completedUnread = false;/, 'let completedUnread = false;\n            let deniedUnread = false;');

fs.writeFileSync(viewPath, text);
console.log('done updating js');
