const fs = require('fs');
let path = 'resources/views/welcome.blade.php';
let text = fs.readFileSync(path, 'utf8');

text = text.replace(/style="object-fit: contain; width: 80%; height: 80%;"/, 'style="object-fit: cover; width: 100%; height: 100%;"');

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing img');
