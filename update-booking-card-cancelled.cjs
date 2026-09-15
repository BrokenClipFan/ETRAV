const fs = require('fs');

let viewPath = 'resources/views/bookings.blade.php';
let text = fs.readFileSync(viewPath, 'utf8');

// Update badges logic to include cancelled
let oldBadgeLogic = `@if($booking->status === 'pending')
                                                <span class="badge status-badge bg-warning-subtle text-warning border border-warning-subtle text-uppercase">Pending Approval</span>
                                            @elseif($booking->status === 'approved')
                                                <span class="badge status-badge bg-info-subtle text-info border border-info-subtle text-uppercase">Awaiting Payment</span>
                                            @elseif($booking->status === 'confirmed')
                                                <span class="badge status-badge bg-success-subtle text-success border border-success-subtle text-uppercase">Confirmed</span>
                                            @elseif($booking->status === 'completed')
                                                <span class="badge status-badge bg-secondary-subtle text-secondary border border-secondary-subtle text-uppercase">Completed</span>
                                            @endif`;

let newBadgeLogic = `@if($booking->status === 'cancelled')
                                                <span class="badge status-badge bg-danger-subtle text-danger border border-danger-subtle text-uppercase">Denied</span>
                                            @elseif($booking->status === 'pending')
                                                <span class="badge status-badge bg-warning-subtle text-warning border border-warning-subtle text-uppercase">Pending Approval</span>
                                            @elseif($booking->status === 'approved')
                                                <span class="badge status-badge bg-info-subtle text-info border border-info-subtle text-uppercase">Awaiting Payment</span>
                                            @elseif($booking->status === 'confirmed')
                                                <span class="badge status-badge bg-success-subtle text-success border border-success-subtle text-uppercase">Confirmed</span>
                                            @elseif($booking->status === 'completed')
                                                <span class="badge status-badge bg-secondary-subtle text-secondary border border-secondary-subtle text-uppercase">Completed</span>
                                            @endif`;
                                            
text = text.replace(new RegExp(oldBadgeLogic.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g'), newBadgeLogic);

// Add the admin message block right before </div> <!-- end card-body -->
let oldFooter = `                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty`;

let newFooter = `                                            @if($booking->admin_message && $booking->status === 'cancelled')
                                                <div class="mt-3 bg-danger-subtle p-2 rounded text-danger" style="font-size: 13px;">
                                                    <i class="bi bi-x-circle-fill me-1"></i><strong>Admin Message:</strong> {{ $booking->admin_message }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty`;

text = text.replace(new RegExp(oldFooter.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g'), newFooter);

fs.writeFileSync(viewPath, text);
console.log('done updating cards');
