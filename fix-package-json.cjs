const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

text = text.replace(/id: \{\{ \$package->id \?\? 0 \}\}/g, "id: {{ (int)($package->id ?? 0) }}");
text = text.replace(/package_price: \{\{ \$package->package_price \?\? 0 \}\}/g, "package_price: {{ (float)($package->package_price ?: 0) }}");
text = text.replace(/max_pax: \{\{ \$package->max_pax \?\? \(\$package->pax_limit \?\? \(\$package->capacity \?\? 10\)\) \}\}/g, "max_pax: {{ (int)($package->max_pax ?: ($package->pax_limit ?: ($package->capacity ?: 10))) }}");

// Vehicles
text = text.replace(/base_price: \{\{ \$vehicle->base_price \?\? 0 \}\}/g, "base_price: {{ (float)($vehicle->base_price ?: 0) }}");
text = text.replace(/interval_rate: \{\{ \$vehicle->interval_rate \?\? 0 \}\}/g, "interval_rate: {{ (float)($vehicle->interval_rate ?: 0) }}");
text = text.replace(/pricing_distance: \{\{ \$vehicle->pricing_distance \?\? 5000 \}\}/g, "pricing_distance: {{ (float)($vehicle->pricing_distance ?: 5000) }}");
text = text.replace(/capacity: \{\{ \$vehicle->capacity \?\? 10 \}\}/g, "capacity: {{ (int)($vehicle->capacity ?: 10) }}");

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing all blade json syntaxes');
