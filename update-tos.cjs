const fs = require('fs');
let text = fs.readFileSync('resources/views/view-package.blade.php', 'utf8');

let newTermsHtml = `
                    <div class="mb-3 p-3 bg-light rounded-3 border text-muted" style="font-size: 12px;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="tosCheckbox">
                            <label class="form-check-label" for="tosCheckbox" style="line-height: 1.4;">
                                <strong>Terms of Service & Cancellation Policy:</strong><br> 
                                By submitting this request, you agree that no payment is required immediately. You must wait for the admin to approve the booking. Once approved, you will be required to pay the 25% deposit. <strong>If you cancel your booking after the 25% deposit has been paid, the deposit is strictly non-refundable.</strong>
                            </label>
                        </div>
                    </div>`;

text = text.replace(/<div class="mb-3 p-3 bg-light rounded-3 border text-muted" style="font-size: 11px;">[\s\S]*?<\/div>/, newTermsHtml.trim());

let newJs = `        function submitBookingRequest() {
            const tos = document.getElementById('tosCheckbox');
            if (tos && !tos.checked) {
                alert("Please agree to the Terms of Service & Cancellation Policy before submitting.");
                return;
            }

            const pickupLat = document.getElementById('pickupLatitude').value;`;

text = text.replace(/function submitBookingRequest\(\) {\s*const pickupLat = document\.getElementById\('pickupLatitude'\)\.value;/, newJs);

fs.writeFileSync('resources/views/view-package.blade.php', text);
console.log('done');
