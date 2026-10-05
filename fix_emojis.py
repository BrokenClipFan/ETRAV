import re

filepath = 'resources/views/admin/packages.blade.php'
with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
    data = f.read()

# Fix corrupted emojis using non-greedy match inside the tags
data = re.sub(r'<h5 class="fw-bold text-dark mb-1" id="formActionHeader">[^<]*Create Package</h5>', '<h5 class="fw-bold text-dark mb-1" id="formActionHeader">✨ Create Package</h5>', data)
data = re.sub(r'document\.getElementById\(\'formActionHeader\'\)\.innerText = "[^"]*Create Package";', 'document.getElementById(\'formActionHeader\').innerText = "✨ Create Package";', data)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(data)
