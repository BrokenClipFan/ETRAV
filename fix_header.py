import re

filepath = 'resources/views/admin/booking-details.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    data = f.read()

# Swap booking ID and package name
data = data.replace(
    'Booking #BKG-{{ $booking->id }}', 
    '{{ $booking->package->name ?? \'Custom Itinerary\' }}'
)
data = data.replace(
    '<p class="text-muted small mb-0">{{ $booking->package->name ?? \'Custom Itinerary\' }}</p>',
    '<p class="text-muted small mb-0">Booking #BKG-{{ $booking->id }}</p>'
)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(data)
