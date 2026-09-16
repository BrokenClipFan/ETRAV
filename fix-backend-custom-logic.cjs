const fs = require('fs');
let path = 'app/Http/Controllers/BookingController.php';
let text = fs.readFileSync(path, 'utf8');

const regex = /\/\/ Check if it's a custom package[\s\S]*?\$isCustomized = true;\n\s*\}\n\s*\}/m;

let match = text.match(regex);
if (match) {
    let oldBlock = match[0];
    let newBlock = `// 4. Check if the itinerary was customized (spots added or removed)
        $isCustomized = false;
        $spotsOrder = $request->input('spots_order', array_keys($validated['duration_hrs'] ?? []));
        $package = \App\Models\Package::with('places')->find($validated['package_id']);
        
        if ($package && count($spotsOrder) !== $package->places->count()) {
            $isCustomized = true;
        } else {
            foreach ($spotsOrder as $placeId) {
                if (str_starts_with((string)$placeId, 'custom_')) {
                    $isCustomized = true;
                    break;
                }
            }
        }
        
        // Check if it's a custom package (spots modified or no package selected)
        $isCustom = $isCustomized || empty($validated['package_id']) || $request->has('custom_spots_name');
        
        $packagePrice = $package ? (float)$package->package_price : 0;
        
        // Final Price Calculation
        $totalPrice = $isCustom ? 0 : $packagePrice;

        $validated['joiners'] = $request->has('allow_joiners');
        $capacity = (int)$vehicle->capacity > 0 ? (int)$vehicle->capacity : 1;

        $perHeadPrice = $totalPrice > 0 ? ($validated['joiners'] ? ($totalPrice / $capacity) : ($totalPrice / $pax)) : 0;
        
        // Downpayment logic
        $depositAmount = $isCustom ? 0 : ($totalPrice * 0.25);
        $status = $isCustom ? 'pending_price' : 'pending';`;
        
    text = text.replace(oldBlock, newBlock);
    fs.writeFileSync(path, text, 'utf8');
    console.log('done fixing backend custom logic order');
} else {
    console.log('regex mismatch');
}
