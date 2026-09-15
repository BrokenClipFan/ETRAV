const fs = require('fs');
let modelPath = 'app/Models/Booking.php';
let text = fs.readFileSync(modelPath, 'utf8');

text = text.replace(/'notify',/, "'notify',\n        'admin_message',");
fs.writeFileSync(modelPath, text);
console.log('done fixing model fillable');
