import re

filepath = 'resources/views/admin/booking-details.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    data = f.read()

old_financials = """                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4"><i class="bi bi-receipt me-2 text-primary"></i>Financials</h6>
                    <div class="d-flex justify-content-between small text-secondary mb-1">
                        <span>Distance-Based Fare ({{ number_format($booking->distance / 1000, 1) }} km):</span>
                        <span>₱{{ number_format($booking->total_price, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between small text-secondary mb-2 border-bottom pb-2">
                        <span>Cost per Passenger:</span>
                        <span>₱{{ number_format($booking->head_price, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-dark fw-medium mb-1" style="font-size: 13px;">
                        <span>Total Gross Cost:</span>
                        <span>₱{{ number_format($booking->total_price, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-dark fw-medium mb-2 border-bottom pb-2" style="font-size: 13px;">
                        <span>Amount Paid:</span>
                        <span class="{{ $booking->amount_paid > 0 ? 'text-success' : 'text-danger' }}">₱{{ number_format($booking->amount_paid, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold text-dark fs-6">
                        <span>Remaining Balance:</span>
                        <span class="text-primary">₱{{ number_format($booking->total_price - $booking->amount_paid, 2) }}</span>
                    </div>"""

new_financials = """                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4"><i class="bi bi-receipt me-2 text-primary"></i>Financials</h6>
                    <div class="d-flex justify-content-between text-dark fw-medium mb-2 border-bottom pb-2" style="font-size: 13px;">
                        <span>Overall Total:</span>
                        <span>₱{{ number_format($booking->total_price, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold text-dark fs-6">
                        <span>25% Payment Amount:</span>
                        <span class="text-primary">₱{{ number_format($booking->total_price * 0.25, 2) }}</span>
                    </div>"""

data = data.replace(old_financials, new_financials)

# One more thing: I noticed the original file had corrupted pesos `,` in the cat output, but my regex might not match if it's actually `₱` in the file.
# Let's use regex to safely replace the block from the `<h6` up to the `Remaining Balance` </div>

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(data)
