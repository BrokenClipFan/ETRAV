const fs = require('fs');
let path = 'resources/views/admin/bookings.blade.php';
let text = fs.readFileSync(path, 'utf8');

const tableRegex = /<div class="table-responsive bg-white rounded-4 shadow-sm border mb-4">[\s\S]*?<\/table>\s*<\/div>/;
let tableMatch = text.match(tableRegex);
if (!tableMatch) {
    console.log("Could not find table!");
    process.exit(1);
}
let tableHtml = tableMatch[0];

let customTableHtml = tableHtml.replace(/\$bookings as \$booking/g, '$customBookings as $booking');
let normalTableHtml = tableHtml.replace(/\$bookings as \$booking/g, '$normalBookings as $booking');

customTableHtml = customTableHtml.replace(/id="manifestTableBody"/g, 'class="manifestTableBody"');
normalTableHtml = normalTableHtml.replace(/id="manifestTableBody"/g, 'class="manifestTableBody"');

customTableHtml = customTableHtml.replace(/id="jsEmptyTableRow"/g, 'class="d-none no-print jsEmptyTableRow"');
normalTableHtml = normalTableHtml.replace(/id="jsEmptyTableRow"/g, 'class="d-none no-print jsEmptyTableRow"');

const newLayout = `
        @php
            $customBookings = $bookings->where('is_custom', true);
            $normalBookings = $bookings->where('is_custom', false);
        @endphp

        <h5 class="fw-bold mb-3 mt-4 text-warning-emphasis"><i class="bi bi-tools me-2"></i> Custom Route Bookings</h5>
        ${customTableHtml}

        <h5 class="fw-bold mb-3 mt-5 text-dark"><i class="bi bi-card-checklist me-2"></i> Standard Bookings</h5>
        ${normalTableHtml}
`;

text = text.replace(tableRegex, newLayout);

let jsRegex = /const cardItems = document\.querySelectorAll\('\.booking-table-row'\);[\s\S]*?if \(visibleCount === 0\) \{[\s\S]*?\}\n\s*\}/m;
let newJs = `const tbodies = document.querySelectorAll('.manifestTableBody');
                    
                    tbodies.forEach(tbody => {
                        const rows = tbody.querySelectorAll('.booking-table-row');
                        const emptyRow = tbody.querySelector('.jsEmptyTableRow');
                        let visibleCount = 0;
                        
                        rows.forEach(card => {
                            const cardStatus = card.getAttribute('data-status');
                            if (targetStatus === 'all' || cardStatus === targetStatus || (targetStatus === 'pending' && cardStatus === 'pending_price')) {
                                card.classList.remove('d-none');
                                visibleCount++;
                            } else {
                                card.classList.add('d-none');
                            }
                        });
                        
                        if (visibleCount === 0) {
                            if (emptyRow) emptyRow.classList.remove('d-none');
                        } else {
                            if (emptyRow) emptyRow.classList.add('d-none');
                        }
                    });`;
text = text.replace(jsRegex, newJs);

fs.writeFileSync(path, text, 'utf8');
console.log("Successfully split into 2 tables!");
