const fs = require('fs');
let text = fs.readFileSync('resources/views/view-package.blade.php', 'utf8');

text = text.replace(/<i class="bi bi-cash-coin me-1 fs-6"><\/i> Pay Now \(25% Deposit\):/, '<i class="bi bi-cash-coin me-1 fs-6"></i> 25% Deposit (Paid After Approval):');

let termsHtml = `
                    <div class="mb-3 p-3 bg-light rounded-3 border text-muted" style="font-size: 11px;">
                        <strong><i class="bi bi-info-circle text-primary"></i> Terms of Service & Cancellation Policy:</strong> 
                        By submitting this request, you agree that no payment is required immediately. You must wait for the admin to approve the booking. Once approved, you will be required to pay the 25% deposit. <strong>If you cancel your booking after the 25% deposit has been paid, the deposit is strictly non-refundable.</strong>
                    </div>
`;

text = text.replace(/<button type="button" class="btn btn-primary w-100 rounded-pill py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-lg sticky-bottom"/, termsHtml + '                    <button type="button" class="btn btn-primary w-100 rounded-pill py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-lg sticky-bottom"');

fs.writeFileSync('resources/views/view-package.blade.php', text);
console.log('done');
