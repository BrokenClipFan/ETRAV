const fs = require('fs');
let viewPath = 'resources/views/bookings.blade.php';
let text = fs.readFileSync(viewPath, 'utf8');

// Replace any occurrence of exactly ',' or '?' directly preceding '{{ number_format'
text = text.replace(/,\{\{ number_format/g, '&#8369;{{ number_format');
text = text.replace(/\?\{\{ number_format/g, '&#8369;{{ number_format');
text = text.replace(/,\{\{/g, '&#8369;{{');
text = text.replace(/\?\{\{/g, '&#8369;{{');

// Replace any occurrence of exactly ',' or '?' directly preceding '{{' or directly after quote
text = text.replace(/data-base="[,\?]/g, 'data-base="&#8369;');
text = text.replace(/data-heads="[,\?]/g, 'data-heads="&#8369;');
text = text.replace(/data-total="[,\?]/g, 'data-total="&#8369;');

// GCash specific
text = text.replace(/'[,\?]\{\{/g, "'&#8369;{{");

fs.writeFileSync(viewPath, text);
console.log('done fixing pesos 2');
