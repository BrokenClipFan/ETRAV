const fs = require('fs');
let viewPath = 'resources/views/bookings.blade.php';
let text = fs.readFileSync(viewPath, 'utf8');

// 1. Container width
text = text.replace(/<div class="container py-5">/g, '<div class="container-xl py-5" style="max-width: 1100px;">');

// 2. Heads -> Passengers
text = text.replace(/Heads<\/strong>/g, 'Passengers</strong>');

// 3. Price formatting & fonts
text = text.replace(/<span class="text-muted small d-block">Total Price:\s*&#8369;\{\{ number_format\(\$booking->total_price, 2\) \}\}<\/span>/g,
    '<span class="text-muted small d-block mb-1">Total Price</span>\n<span class="fs-5 fw-bold text-dark">&#8369;{{ number_format($booking->total_price, 2) }}</span>');

text = text.replace(/Date:\s*<strong>/g, 'Date: <strong class="text-dark fs-6">');
text = text.replace(/Pickup:\s*<strong>/g, 'Pickup: <strong class="text-dark fs-6">');
text = text.replace(/Travelers:\s*<strong>/g, 'Travelers: <strong class="text-dark fs-6">');

// 4. Badges overhaul for ALL tabs
let complexBadgeRegex = /<span\s+class="badge status-badge \{\{ \$booking->status === 'pending'[^>]*>\s*\{\{ \$booking->status \}\}\s*<\/span>/g;
let simpleBadgeRegex = /<span class="badge status-badge bg-[^-]+-subtle text-[^\s]+ border border-[^-]+-subtle text-uppercase">[^<]+<\/span>/g;

let newBadgeBlock = `@php
$badgeClass = match($booking->status) {
    'pending' => 'bg-primary-subtle text-primary border border-primary-subtle',
    'approved' => 'bg-info-subtle text-info border border-info-subtle',
    'confirmed' => 'bg-success-subtle text-success border border-success-subtle',
    'completed' => 'bg-dark-subtle text-dark border border-dark-subtle',
    'denied', 'cancelled' => 'bg-danger-subtle text-danger border border-danger-subtle',
    default => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
};
@endphp
<span class="badge status-badge {{ $badgeClass }} text-uppercase">{{ $booking->status }}</span>`;

text = text.replace(complexBadgeRegex, newBadgeBlock);
text = text.replace(simpleBadgeRegex, newBadgeBlock); // For the hardcoded 'Denied', 'Completed' ones in other tabs

// 5. Redundant text status under price (from tab-all and tab-pending)
let redundantIfBlock1 = /@if\(\$booking->status === 'pending'\)\s*<div class="fw-bold text-secondary" style="font-size: 15px;">Awaiting Approval<\/div>\s*@elseif\(\$booking->status === 'approved'\)\s*<div class="fw-bold text-danger" style="font-size: 15px;">25% Deposit Due: &#8369;\{\{ number_format\(\$booking->deposit_amount, 2\) \}\}<\/div>\s*@elseif\(\$booking->status === 'confirmed'\)\s*@if\(\$booking->amount_paid >= \$booking->deposit_amount\)\s*<div class="fw-bold text-success" style="font-size: 15px;">25% Deposit Paid<\/div>\s*@else\s*<div class="fw-bold text-warning" style="font-size: 15px;">Payment Pending<\/div>\s*@endif\s*@elseif\(\$booking->status === 'completed'\)\s*<div class="fw-bold text-success" style="font-size: 15px;">Finished<\/div>\s*@elseif\(\$booking->status === 'denied'\)\s*<div class="fw-bold text-danger" style="font-size: 15px;">Denied<\/div>\s*@elseif\(\$booking->status === 'cancelled'\)\s*<div class="fw-bold text-danger" style="font-size: 15px;">Cancelled<\/div>\s*@endif/g;

let newDepositBlock = `@if($booking->status === 'approved')
    <div class="fw-bold text-danger" style="font-size: 15px;">25% Deposit Due: &#8369;{{ number_format($booking->deposit_amount, 2) }}</div>
@elseif($booking->status === 'confirmed')
    @if($booking->amount_paid >= $booking->deposit_amount)
        <div class="fw-bold text-success" style="font-size: 15px;"><i class="bi bi-check-circle-fill me-1"></i>25% Deposit Paid</div>
    @else
        <div class="fw-bold text-warning" style="font-size: 15px;"><i class="bi bi-clock-fill me-1"></i>Payment Pending</div>
    @endif
@endif`;
text = text.replace(redundantIfBlock1, newDepositBlock);

// Remove hardcoded text statuses in specific tabs
text = text.replace(/<div class="fw-bold text-success"[^>]*>Finished<\/div>/g, '');
text = text.replace(/<div class="fw-bold text-danger"[^>]*>Denied<\/div>/g, '');
text = text.replace(/<div class="fw-bold text-danger"[^>]*>Cancelled<\/div>/g, '');
text = text.replace(/<div class="fw-bold text-success"[^>]*>25% Deposit Paid<\/div>/g, '');

// 6. Admin Message Move
// First, remove old admin message blocks
let oldAdminBlock = /@if\(\$booking->admin_message\)\s*<div class="mt-3 bg-danger-subtle p-2 rounded text-danger" style="font-size: 13px;">\s*<i class="bi bi-x-circle-fill me-1"><\/i><strong>Admin Message:<\/strong> \{\{ \$booking->admin_message \}\}\s*<\/div>\s*@endif/g;
let oldAdminBlockDeniedOnly = /@if\(\$booking->admin_message && \$booking->status === 'denied'\)\s*<div class="mt-3 bg-danger-subtle p-2 rounded text-danger" style="font-size: 13px;">\s*<i class="bi bi-x-circle-fill me-1"><\/i><strong>Admin Message:<\/strong> \{\{ \$booking->admin_message \}\}\s*<\/div>\s*@endif/g;

text = text.replace(oldAdminBlock, '');
text = text.replace(oldAdminBlockDeniedOnly, '');

// Then inject new admin message under the header badge row
let newAdminMessage = `
                                          @if($booking->admin_message && in_array($booking->status, ['denied', 'cancelled']))
                                              <div class="mt-1 mb-3 bg-danger-subtle p-3 rounded-3 text-danger border border-danger-subtle shadow-sm">
                                                  <div class="d-flex align-items-start gap-2">
                                                      <i class="bi bi-x-circle-fill mt-1 fs-5"></i>
                                                      <div>
                                                          <strong class="d-block mb-1" style="font-size: 14px;">Reason for Rejection</strong>
                                                          <span style="font-size: 13.5px; opacity: 0.9;">{{ $booking->admin_message }}</span>
                                                      </div>
                                                  </div>
                                              </div>
                                          @endif
`;
// Target insertion point: right after the flex container of the header
text = text.replace(/<\/span>\s*<\/div>\s*(<div class="row[^\n]+text-muted small">)/g, function(match, p1) {
    return `</span>\n                                          </div>\n` + newAdminMessage + `                                          ` + p1;
});

// 7. Add Rebook Action Button
text = text.replace(/<button class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-medium"[^>]*onclick="viewItineraryDetails\([^>]*>\s*<i class="bi bi-eye"><\/i> View Details\s*<\/button>/g, function(match) {
    let rebookButton = `\n                                                  @if(in_array($booking->status, ['denied', 'cancelled']))
                                                      <a href="{{ $booking->package_id ? route('package.book', $booking->package_id) : route('custom.package.book') }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-medium shadow-sm"><i class="bi bi-arrow-repeat me-1"></i>Rebook</a>
                                                  @endif`;
    return match + rebookButton;
});

fs.writeFileSync(viewPath, text);
console.log('done ui fixes');
