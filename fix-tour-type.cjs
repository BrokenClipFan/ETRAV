const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

const oldJs = `document.getElementById('breakdownBase').innerText = isJoinerAllowed ?
          \`Joiner / Open Group (\$\{headsCount\} slots)\` :
          'Private / Exclusive Group';`;

const newJs = `document.getElementById('breakdownBase').innerText = isJoinerAllowed ?
          'Joiner / Open Group' :
          'Private / Exclusive Group';`;

text = text.replace(oldJs, newJs);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing tour type');
