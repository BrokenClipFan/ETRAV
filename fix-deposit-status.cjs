const fs = require('fs');
let viewPath = 'resources/views/bookings.blade.php';
let text = fs.readFileSync(viewPath, 'utf8');

// Replace tab-all snippet
let regex1 = /<div class="fw-bold \{\{ \$booking->status === 'pending' \? 'text-secondary' : \(\$booking->status === 'approved' \? 'text-danger' : 'text-success'\) \}\}"\s+style="font-size: 15px;">\s+\{\{ \$booking->status === 'pending' \? 'Awaiting Approval' : \(\$booking->status === 'approved' \? '25% Deposit Due: [^']*' \. number_format\(\$booking->deposit_amount, 2\) : '25% Deposit Paid'\) \}\}\s+<\/div>/;

let cleanStatusBlock = `
                                                  @if($booking->status === 'pending')
                                                      <div class="fw-bold text-secondary" style="font-size: 15px;">Awaiting Approval</div>
                                                  @elseif($booking->status === 'approved')
                                                      <div class="fw-bold text-danger" style="font-size: 15px;">25% Deposit Due: &#8369;{{ number_format($booking->deposit_amount, 2) }}</div>
                                                  @elseif($booking->status === 'confirmed')
                                                      @if($booking->amount_paid >= $booking->deposit_amount)
                                                          <div class="fw-bold text-success" style="font-size: 15px;">25% Deposit Paid</div>
                                                      @else
                                                          <div class="fw-bold text-warning" style="font-size: 15px;">Payment Pending</div>
                                                      @endif
                                                  @elseif($booking->status === 'completed')
                                                      <div class="fw-bold text-success" style="font-size: 15px;">Finished</div>
                                                  @elseif($booking->status === 'denied')
                                                      <div class="fw-bold text-danger" style="font-size: 15px;">Denied</div>
                                                  @elseif($booking->status === 'cancelled')
                                                      <div class="fw-bold text-danger" style="font-size: 15px;">Cancelled</div>
                                                  @endif
`;

text = text.replace(regex1, cleanStatusBlock.trim());

// Also replace tab-pending snippet just to be consistent
let regex2 = /<div class="fw-bold \{\{ \$booking->status === 'pending' \? 'text-secondary' : 'text-danger' \}\}">\s+\{\{ \$booking->status === 'pending' \? 'Awaiting Approval' : '25% Deposit Due: [^']*' \. number_format\(\$booking->deposit_amount, 2\) \}\}\s+<\/div>/;

text = text.replace(regex2, cleanStatusBlock.trim());

fs.writeFileSync(viewPath, text);
console.log('done fixing deposit logic');
