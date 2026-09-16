const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

// We have lines like: lat: {{ $place->latitude ?? ($place->lat ?? 0) }},
text = text.replace(/lat: \{\{\s*\$place->latitude \?\? \(\$place->lat \?\? 0\)\s*\}\}/g, "lat: {{ (float)($place->latitude ?: ($place->lat ?: 0)) }}");
text = text.replace(/lng: \{\{\s*\$place->longitude \?\? \(\$place->lng \?\? 0\)\s*\}\}/g, "lng: {{ (float)($place->longitude ?: ($place->lng ?: 0)) }}");

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing lat lng comma syntax');
