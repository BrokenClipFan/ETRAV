const fs = require('fs');
let path = 'resources/views/admin/bookings.blade.php';
let text = fs.readFileSync(path, 'utf8');

// Update filter script
text = text.replace(
    /if \(targetStatus === 'all' \|\| cardStatus === targetStatus\) \{/g,
    "if (targetStatus === 'all' || cardStatus === targetStatus || (targetStatus === 'pending' && cardStatus === 'pending_price')) {"
);

// Update hasNewPending logic
text = text.replace(
    /\$hasNewPending = \$bookings->where\('status', 'pending'\)->count\(\) > 0;/g,
    "$hasNewPending = $bookings->whereIn('status', ['pending', 'pending_price'])->count() > 0;"
);

// Update badges in HTML
text = text.replace(
    /@if\(\$booking->status === 'pending'\)/g,
    "@if(in_array($booking->status, ['pending', 'pending_price']))"
);

const badgeHtml = `
                                @elseif($booking->status === 'pending_price')
                                    <span class="badge bg-warning text-dark border border-warning rounded-pill px-2"><i class="bi bi-tag-fill me-1"></i> Needs Quote</span>
                                @elseif($booking->status === 'approved')`;

text = text.replace(/@elseif\(\$booking->status === 'approved'\)/g, badgeHtml);

// Wait, I should also make sure the OrderByRaw in the Controller considers pending_price.
fs.writeFileSync(path, text, 'utf8');
console.log('Fixed admin bookings view');
