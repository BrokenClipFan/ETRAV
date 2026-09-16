const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

// 1. Declare let isCustomRoute = false; at the top of the JS context
text = text.replace('let activePaxLimit = 10;', 'let activePaxLimit = 10;\n        let isCustomRoute = false;');

// 2. Set isCustomRoute = true when a custom spot is added
text = text.replace('packageData[activePackageId].spots.push(newSpot);', 'packageData[activePackageId].spots.push(newSpot);\n            isCustomRoute = true;\n            updateSubmitButtonText();');

// 3. Set isCustomRoute = true when a custom spot is dragged
text = text.replace('spot.lat = position.lat;\n                        spot.lng = position.lng;', 'spot.lat = position.lat;\n                        spot.lng = position.lng;\n                        isCustomRoute = true;\n                        updateSubmitButtonText();');

// 4. Update the submit button text based on isCustomRoute
const btnRegex = /<button type="button" class="btn btn-primary w-100 rounded-pill py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-lg sticky-bottom" style="bottom: 10px; font-size: 15px;" onclick="submitBookingRequest\(\)">[\s\S]*?<\/button>/m;
const newBtn = `<button type="button" id="submitBookingBtn" class="btn btn-primary w-100 rounded-pill py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-lg sticky-bottom" style="bottom: 10px; font-size: 15px;" onclick="submitBookingRequest()">
                        <i class="bi bi-calendar-check"></i> Book Now
                    </button>`;
text = text.replace(btnRegex, newBtn);

const newJsFunction = `function updateSubmitButtonText() {
            const btn = document.getElementById('submitBookingBtn');
            if (btn) {
                if (isCustomRoute) {
                    btn.innerHTML = '<i class="bi bi-chat-quote"></i> Request Custom Quote';
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-warning');
                } else {
                    btn.innerHTML = '<i class="bi bi-calendar-check"></i> Book Now';
                    btn.classList.remove('btn-warning');
                    btn.classList.add('btn-primary');
                }
            }
        }
        
        function submitBookingRequest() {`;
        
text = text.replace('function submitBookingRequest() {', newJsFunction);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing custom route flag');
