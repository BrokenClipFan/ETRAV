const fs = require('fs');
const text = fs.readFileSync('resources/views/view-package.blade.php', 'utf8');

const regex = /document\.getElementById\('([^']+)'\)\.innerText/g;
let match;
while ((match = regex.exec(text)) !== null) {
    const id = match[1];
    if (!text.includes(`id="${id}"`) && !text.includes(`id='${id}'`)) {
        console.log(`MISSING ID IN HTML: ${id}`);
    } else {
        console.log(`FOUND ID: ${id}`);
    }
}
