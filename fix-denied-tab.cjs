const fs = require('fs');
let viewPath = 'resources/views/bookings.blade.php';
let text = fs.readFileSync(viewPath, 'utf8');

let regex = /<!-- TAB: DENIED -->[\s\S]*?@empty/;

let correctTabDenied = `<!-- TAB: DENIED -->
              <div class="tab-pane fade" id="tab-denied" role="tabpanel">
                  <div class="row g-4">
                      @forelse($bookings->where('status', 'denied') as $booking)
                          <div class="col-12">
                              <div
                                  class="card mb-4 shadow-sm rounded-4 booking-card bg-white overflow-hidden booking-card-{{ $booking->id }} {{ $booking->notify ? 'border-2 border-danger' : 'border' }}">
                                  <div class="row g-0">
                                      <div class="col-md-3 bg-secondary-subtle d-flex align-items-center justify-content-center text-muted border-end position-relative"
                                          style="min-height: 140px;">
                                          @if (isset($booking->package->image_path))
                                              <img src="{{ $booking->package->image_path }}"
                                                  alt="{{ $booking->package->name }}"
                                                  class="w-100 h-100 position-absolute" style="object-fit: cover;">
                                          @else
                                              <div class="text-center p-3">
                                                  <i class="bi bi-image fs-1 d-block opacity-50 mb-1"></i>
                                                  <span class="small">No Preview</span>
                                              </div>
                                          @endif
                                      </div>
                                      <div class="col-md-9 p-4">
                                          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                              <div>
                                                  <span class="text-muted font-monospace small d-flex align-items-center gap-2 mb-1">
                                                      BOOKING-ID: #ETV-{{ $booking->id }}
                                                      <span class="badge bg-danger rounded-pill px-2 notify-badge-{{ $booking->id }} {{ $booking->notify ? '' : 'd-none' }}" style="font-size: 9px; letter-spacing: 0.3px;">NEW UPDATE</span>
                                                  </span>
                                                  <h5 class="fw-bold text-dark mb-0">{{ $booking->package->name ?? 'Custom Package' }}</h5>
                                              </div>
                                              <span class="badge status-badge bg-danger-subtle text-danger border border-danger-subtle text-uppercase">Denied</span>
                                          </div>
                                          <div class="row g-3 my-2 text-muted small">
                                              <div class="col-sm-4"><i class="bi bi-calendar3 text-primary me-1"></i>
                                                  Date: <strong>{{ date('M d, Y', strtotime($booking->pickup_datetime)) }}</strong>
                                              </div>
                                              <div class="col-sm-4"><i class="bi bi-clock text-primary me-1"></i>
                                                  Pickup: <strong>{{ date('h:i A', strtotime($booking->pickup_datetime)) }}</strong>
                                              </div>
                                              <div class="col-sm-4"><i class="bi bi-people text-primary me-1"></i>
                                                  Travelers: <strong>{{ $booking->pax }} Heads</strong></div>
                                          </div>
                                          <div class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top flex-wrap gap-3">
                                              <div>
                                                  <span class="text-muted small d-block">Total Price: &#8369;{{ number_format($booking->total_price, 2) }}</span>
                                                  <div class="fw-bold text-danger" style="font-size: 15px;">Denied</div>
                                              </div>
                                              <div class="d-flex gap-2">
                                                  <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-medium"
                                                      onclick="viewItineraryDetails(this)"
                                                      data-id="{{ $booking->id }}"
                                                      data-notify="{{ $booking->notify ? '1' : '0' }}"
                                                      data-status="{{ $booking->status }}"
                                                      data-title="{{ $booking->package->name ?? 'Package Specification' }}"
                                                      data-pickup="{{ $booking->pickup_place_name }}"
                                                      data-base="&#8369;{{ number_format($booking->total_price, 2) }} ({{ number_format($booking->distance / 1000, 1) }} km)"
                                                      data-heads="&#8369;{{ number_format($booking->head_price, 2) }} / person"
                                                      data-total="&#8369;{{ number_format($booking->total_price, 2) }}"
                                                      data-places="{{ $booking->itinerary->map(fn($i) => ['name' => $i->place ? $i->place->name : ($i->custom_name ?? 'Custom Stop'), 'description' => $i->place ? ($i->place->description ?? 'Included destination.') : 'Custom destination pinned by you.'])->toJson() }}">
                                                      <i class="bi bi-eye"></i> View Details
                                                  </button>
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
                      @empty`;

text = text.replace(regex, correctTabDenied);
fs.writeFileSync(viewPath, text);
console.log('done fixing tab-denied items');
