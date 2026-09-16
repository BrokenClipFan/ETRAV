const fs = require('fs');
let path = 'resources/views/welcome.blade.php';
let text = fs.readFileSync(path, 'utf8');

text = text.replace(/'best combo'/g, "'best_combo'");
text = text.replace(/'budget friendly'/g, "'budget'");

// Also let's replace the display text because `strtoupper('best_combo')` becomes `BEST_COMBO`.
let oldDisplay = `{{ strtoupper($type) }}`;
let newDisplay = `{{ strtoupper(str_replace('_', ' ', $type)) }}`;

text = text.replace(oldDisplay, newDisplay);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing match');
