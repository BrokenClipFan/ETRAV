const fs = require('fs');
let viewPath = 'resources/views/bookings.blade.php';
let text = fs.readFileSync(viewPath, 'utf8');

text = text.replace(/<i class="bi bi-eye"><\/i> View Details\s*<\/button>/g, function(match) {
    let rebookButton = `\n                                                  @if(in_array($booking->status, ['denied', 'cancelled']))
                                                      <a href="{{ $booking->package_id ? route('package.book', $booking->package_id) : route('custom.package.book') }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-medium shadow-sm"><i class="bi bi-arrow-repeat me-1"></i>Rebook</a>
                                                  @endif`;
    return match + rebookButton;
});

fs.writeFileSync(viewPath, text);
console.log('done fixing rebook button');
