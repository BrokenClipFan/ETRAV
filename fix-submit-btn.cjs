const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

const jsSetup = `
        function updateSubmitButtonText() {
            const btn = document.querySelector('button[onclick="submitBookingRequest()"]');
            if (btn) {
                if (isCustomRoute) {
                    btn.innerHTML = '<i class="bi bi-calendar-check"></i> Request Custom Quote';
                    btn.classList.replace('btn-primary', 'btn-warning');
                    btn.classList.add('text-dark');
                } else {
                    btn.innerHTML = '<i class="bi bi-calendar-check"></i> Submit Booking Request';
                    btn.classList.replace('btn-warning', 'btn-primary');
                    btn.classList.remove('text-dark');
                }
            }
        }
`;

// Insert it right after the customWarningShown declaration
text = text.replace('let customWarningShown = false;', 'let customWarningShown = false;\n' + jsSetup);

fs.writeFileSync(path, text, 'utf8');
console.log('done adding updateSubmitButtonText');
