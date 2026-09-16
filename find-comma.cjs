const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let lines = fs.readFileSync(path, 'utf8').split('\n');

for (let i = 0; i < lines.length; i++) {
    const line = lines[i];
    if (line.length >= 10 && line[9] === ',') {
        console.log(`Line ${i + 1}: ${line}`);
    }
}
