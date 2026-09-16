const fs = require('fs');
let path = 'resources/views/welcome.blade.php';
let text = fs.readFileSync(path, 'utf8');

text = text.replace(/%3C%2Fsvg%3E' ' \. \$vehicle->model\) \}\}'\;"/g, '%3C%2Fsvg%3E\';"');

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing vehicle img');
