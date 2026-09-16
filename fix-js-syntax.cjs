const fs = require('fs');
let path = 'resources/views/admin/bookings.blade.php';
let text = fs.readFileSync(path, 'utf8');

text = text.replace(/const filterButtons = document\.querySelectorAll\('\.filter-btn'\);\s*const tbodies = document\.querySelectorAll\('\.manifestTableBody'\);[\s\S]*?\}\);\s*\};\);/m,
`const filterButtons = document.querySelectorAll('.filter-btn');
            const tbodies = document.querySelectorAll('.manifestTableBody');
            
            filterButtons.forEach(btn => {
                btn.addEventListener('click', function() {
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
                        
                        if (visibleCount === 0) {
                            if (emptyRow) emptyRow.classList.remove('d-none');
                        } else {
                            if (emptyRow) emptyRow.classList.add('d-none');
                        }
                    });
                });`);

fs.writeFileSync(path, text, 'utf8');
console.log('Fixed js syntax');
