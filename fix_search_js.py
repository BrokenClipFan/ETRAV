import re

filepath = 'resources/views/admin/transport.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    data = f.read()

# Fix search query selector
old_js = "const title = card.querySelector('h6')?.innerText.toLowerCase() || '';"
new_js = "const title = card.querySelector('.fw-bold')?.innerText.toLowerCase() || '';"
data = data.replace(old_js, new_js)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(data)
