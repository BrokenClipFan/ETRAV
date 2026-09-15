const fs = require('fs');
let text = fs.readFileSync('resources/views/bookings.blade.php', 'utf8');

// 1. Restore buttons format and add Cancel button inside
text = text.replace(/<div class="d-flex flex-wrap gap-2 justify-content-end w-100">\s*([\s\S]*?)<\/button>\s*(@if\(\$booking->status === 'approved'\)[\s\S]*?@endif)?\s*<\/div>/g, (match, p1, p2) => {
    let p2Safe = p2 || '';
    return `<div class="d-flex flex-wrap gap-2 justify-content-between w-100">
    <div>
        @if(in_array($booking->status, ['pending', 'approved', 'confirmed']))
            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-medium shadow-sm" onclick="confirmCancel({{ $booking->id }}, '{{ $booking->status }}')">Cancel Booking</button>
        @endif
    </div>
    <div class="d-flex flex-wrap gap-2">
        ${p1.trim()}</button>
        ${p2Safe.trim()}
    </div>
</div>`;
});

// Other tabs
text = text.replace(/<div class="d-flex gap-2">\s*([\s\S]*?)<\/button>\s*(@if\(\$booking->status === 'approved'\)[\s\S]*?@endif)?\s*<\/div>/g, (match, p1, p2) => {
    let p2Safe = p2 || '';
    return `<div class="d-flex flex-wrap gap-2 justify-content-between w-100">
    <div>
        @if(in_array($booking->status, ['pending', 'approved', 'confirmed']))
            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-medium shadow-sm" onclick="confirmCancel({{ $booking->id }}, '{{ $booking->status }}')">Cancel Booking</button>
        @endif
    </div>
    <div class="d-flex flex-wrap gap-2">
        ${p1.trim()}</button>
        ${p2Safe.trim()}
    </div>
</div>`;
});

if (!text.includes('id="cancelModal"')) {
    let modalHtml = `
    <!-- CANCEL BOOKING MODAL -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 pb-0 justify-content-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4 pt-0">
                    <i class="bi bi-exclamation-triangle text-danger mb-3" style="font-size: 3rem;"></i>
                    <h5 class="fw-bold text-dark mb-2">Cancel Booking?</h5>
                    
                    <p class="text-muted small mb-4" id="cancelModalMessage">
                        Are you sure you want to cancel this booking? This action cannot be undone.
                    </p>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light rounded-pill w-50 fw-medium" data-bs-dismiss="modal">Keep It</button>
                        <button type="button" class="btn btn-danger rounded-pill w-50 fw-bold" onclick="submitCancelForm()">Cancel It</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- HIDDEN FORM FOR CANCELLATION -->
    <form id="cancelBookingForm" method="POST" action="" style="display: none;">
        @csrf
    </form>
`;
    text = text.replace('<!-- Bootstrap Bundle JS -->', modalHtml + '\n    <!-- Bootstrap Bundle JS -->');
}

if (!text.includes('function confirmCancel')) {
    let jsCode = `
        let currentCancelBookingId = null;

        function confirmCancel(bookingId, status) {
            currentCancelBookingId = bookingId;
            let messageBox = document.getElementById('cancelModalMessage');
            
            if (status === 'confirmed') {
                messageBox.innerHTML = 'This booking is already paid. If you cancel now, your <strong>25% deposit is strictly non-refundable</strong>. Are you sure you want to proceed?';
            } else {
                messageBox.innerHTML = "Are you sure you want to cancel this booking? This action cannot be undone.";
            }
            
            new bootstrap.Modal(document.getElementById('cancelModal')).show();
        }

        function submitCancelForm() {
            if (currentCancelBookingId) {
                const form = document.getElementById('cancelBookingForm');
                form.action = \`/booking/\${currentCancelBookingId}/cancel\`;
                form.submit();
            }
        }
`;
    text = text.replace('</script>', jsCode + '\n    </script>');
}

fs.writeFileSync('resources/views/bookings.blade.php', text);
console.log("All fixes applied!");
