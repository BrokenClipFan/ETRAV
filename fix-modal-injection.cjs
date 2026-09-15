const fs = require('fs');
let viewPath = 'resources/views/admin/booking-details.blade.php';
let text = fs.readFileSync(viewPath, 'utf8');

let modalHtml = `
    <!-- Deny Modal -->
    <div class="modal fade" id="denyModal" tabindex="-1" aria-labelledby="denyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="{{ route('admin.booking.deny', $booking->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger" id="denyModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i>Deny Booking</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 pt-3">
                        <p class="text-muted small mb-3">You are about to cancel this booking and free up the vehicle. Please provide a reason to the user.</p>
                        <div class="mb-3">
                            <label for="admin_message" class="form-label fw-bold text-dark small">Reason / Message</label>
                            <textarea class="form-control rounded-3" id="admin_message" name="admin_message" rows="3" placeholder="e.g., We are fully booked for this vehicle today." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Deny Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
`;

text = text.replace(/<\/body>/, modalHtml + '\n</body>');
fs.writeFileSync(viewPath, text);
console.log('done');
