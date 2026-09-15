const fs = require('fs');
let text = fs.readFileSync('resources/views/view-package.blade.php', 'utf8');

// Add required attribute
text = text.replace(/<input class="form-check-input" type="checkbox" id="tosCheckbox">/, '<input class="form-check-input" type="checkbox" id="tosCheckbox" required>');

// Update submitBookingRequest
let oldJs = `        function submitBookingRequest() {
            const tos = document.getElementById('tosCheckbox');
            if (tos && !tos.checked) {
                alert("Please agree to the Terms of Service & Cancellation Policy before submitting.");
                return;
            }

            const pickupLat = document.getElementById('pickupLatitude').value;`;

let newJs = `        function submitBookingRequest() {
            const form = document.getElementById('bookingForm');
            
            // Check native HTML5 validation (will highlight the required checkbox)
            if (!form.reportValidity()) {
                return;
            }

            const pickupLat = document.getElementById('pickupLatitude').value;`;

text = text.replace(oldJs, newJs);

fs.writeFileSync('resources/views/view-package.blade.php', text);
console.log('done');
