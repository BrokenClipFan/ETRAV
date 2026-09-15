const fs = require('fs');
let viewPath = 'resources/views/bookings.blade.php';
let text = fs.readFileSync(viewPath, 'utf8');

let newTabPane = `            <!-- TAB: DENIED -->
            <div class="tab-pane fade" id="tab-denied" role="tabpanel">
                <div class="row g-4">
                    @forelse($bookings->where('status', 'cancelled') as $booking)
                        <div class="col-12">
                            <div
                                class="card mb-4 shadow-sm rounded-4 booking-card bg-white overflow-hidden booking-card-{{ $booking->id }} {{ $booking->notify ? 'border-2 border-danger' : 'border' }}">
                                <div class="row g-0">
                                    <div class="col-md-3 bg-secondary-subtle position-relative">
                                        @if (isset($booking->package->image_path))
                                            <img src="{{ $booking->package->image_path }}"
                                                class="w-100 h-100 position-absolute" style="object-fit: cover;">
                                        @endif
                                    </div>
                                    <div class="col-md-9 p-4">
                                        <div class="d-flex justify-content-between mb-2">
                                            <h5 class="fw-bold text-dark mb-0">
                                                {{ $booking->package->name ?? 'Custom Package' }}
                                                <span
                                                    class="badge bg-danger rounded-pill px-2 ms-1 notify-badge-{{ $booking->id }} {{ $booking->notify ? '' : 'd-none' }}"
                                                    style="font-size: 9px;">NEW UPDATE</span>
                                            </h5>
                                            <span
                                                class="badge status-badge bg-danger-subtle text-danger border border-danger-subtle text-uppercase">Denied</span>
                                        </div>
                                        <div class="row my-2 text-muted small">
                                            <div class="col-sm-4">Date:
                                                <strong>{{ date('M d, Y', strtotime($booking->pickup_datetime)) }}</strong>
                                            </div>
                                            <div class="col-sm-4">Pickup:
                                                <strong>{{ date('h:i A', strtotime($booking->pickup_datetime)) }}</strong>
                                            </div>
                                            <div class="col-sm-4">Travelers: <strong>{{ $booking->pax }}
                                                    Heads</strong></div>
                                        </div>
                                        <div
                                            class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top">
                                            <div><span class="text-muted small d-block">Total Price:
                                                    ?{{ number_format($booking->total_price, 2) }}</span>
                                                <div class="fw-bold text-danger">Cancelled</div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4 fw-medium shadow-sm"
                                                    data-id="{{ $booking->id }}"
                                                    data-notify="{{ $booking->notify ? '1' : '0' }}"
                                                    data-title="{{ $booking->package->name ?? 'Custom Package' }}"
                                                    data-pickup="{{ $booking->pickup_place_name }}"
                                                    data-base="?{{ number_format($booking->vehicle->base_price ?? 0, 2) }}"
                                                    data-heads="?{{ number_format($booking->head_price, 2) }}"
                                                    data-total="?{{ number_format($booking->total_price, 2) }}"
                                                    data-places="{{ $booking->itinerary->map(fn($i) => ['name' => $i->place ? $i->place->name : ($i->custom_name ?? 'Custom Stop'), 'description' => $i->place ? ($i->place->description ?? 'Included destination.') : 'Custom destination pinned by you.'])->toJson() }}"
                                                    onclick="viewItineraryDetails(this)"><i
                                                    class="bi bi-eye"></i> View Details</button>
                                            </div>
                                        </div>
                                        
                                        @if($booking->admin_message)
                                            <div class="mt-3 bg-danger-subtle p-2 rounded text-danger" style="font-size: 13px;">
                                                <i class="bi bi-x-circle-fill me-1"></i><strong>Admin Message:</strong> {{ $booking->admin_message }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-x-circle text-muted mb-3" style="font-size: 3rem;"></i>
                            <h5 class="fw-bold text-dark">No Denied Bookings</h5>
                            <p class="text-muted mb-0">None of your bookings have been rejected.</p>
                        </div>
                    @endforelse
                </div>
            </div>
`;

text = text.replace('<!-- 3. DETAILED SPECIFICATION MODAL OVERLAY -->', newTabPane + '<!-- 3. DETAILED SPECIFICATION MODAL OVERLAY -->');

fs.writeFileSync(viewPath, text);
console.log('done fixing tab pane');
