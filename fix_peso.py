import re

filepath = 'resources/views/admin/packages.blade.php'
with open(filepath, 'rb') as f:
    data = f.read()

# Replace any garbled Entrance Fee label
decoded = data.decode('utf-8', errors='replace')
decoded = re.sub(r'<label class="form-label spot-modal-label">Entrance Fee \([^)]*\)</label>', '<label class="form-label spot-modal-label">Entrance Fee (₱)</label>', decoded)

# Also check for other garbled pesos if they exist
decoded = decoded.replace('â‚±', '₱')
decoded = decoded.replace('₱', '₱') # Just to be safe

with open(filepath, 'wb') as f:
    f.write(decoded.encode('utf-8'))
