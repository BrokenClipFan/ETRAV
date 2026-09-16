const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');

const modalHtml = `
<!-- Custom Route Warning Modal -->
<div class="modal fade" id="customRouteWarningModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 bg-warning-subtle text-warning-emphasis rounded-top-4 p-4 pb-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Custom Package Notice</h5>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-4">
                    <i class="bi bi-chat-quote text-warning" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-3">You are modifying a fixed package!</h5>
                <p class="text-muted mb-0">
                    By adding, removing, or changing spots, your itinerary will be converted into a <strong>Custom Package</strong>.
                </p>
                <p class="text-muted mt-2">
                    The fixed package price will be removed, and an admin will review your requested route to provide a custom price quote before you can pay the deposit.
                </p>
            </div>
            <div class="modal-footer border-top-0 p-4 pt-0">
                <button type="button" class="btn btn-warning w-100 rounded-pill fw-bold" id="understandWarningBtn" disabled>
                    I Understand (5s)
                </button>
            </div>
        </div>
    </div>
</div>
`;

if (!text.includes('id="customRouteWarningModal"')) {
    text = text.replace('</body>', modalHtml + '\n</body>');
    fs.writeFileSync(path, text, 'utf8');
    console.log('injected html');
} else {
    console.log('already injected');
}
