const fs = require('fs');
let path = 'resources/views/admin/bookings.blade.php';
let text = fs.readFileSync(path, 'utf8');

const oldJsStart = "const filterButtons = document.querySelectorAll('.filter-btn');";
const oldJsEnd = "            });";

let startIndex = text.indexOf(oldJsStart);
// find the correct oldJsEnd by looking for the one right before "const defaultFilter ="
let endIndex = text.indexOf("const defaultFilter =");
// go backwards from endIndex to find the closing brackets
let blockToReplace = text.substring(startIndex, endIndex);

let newJs = `const filterButtons = document.querySelectorAll('.filter-btn');
            const tbodies = document.querySelectorAll('.manifestTableBody');

            filterButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    filterButtons.forEach(b => {
                        b.classList.remove('btn-primary');
                        b.classList.add('btn-light', 'text-muted');
                    });
                    this.classList.remove('btn-light', 'text-muted');
                    this.classList.add('btn-primary');

                    const targetStatus = this.getAttribute('data-filter');

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

                        if (emptyRow) {
                            if (visibleCount === 0) {
                                emptyRow.classList.remove('d-none');
                            } else {
                                emptyRow.classList.add('d-none');
                            }
                        }
                    });
                });
            });

            `;

text = text.substring(0, startIndex) + newJs + text.substring(endIndex);

fs.writeFileSync(path, text, 'utf8');
console.log("Successfully rewrote JS block!");
