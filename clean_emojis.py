import re

filepath = 'resources/views/admin/packages.blade.php'
with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
    data = f.read()

replacements = [
    (r'o" Create Package', '✨ Create Package'),
    (r'A,\?\?A Popular', '🔥 Popular'),
    (r'AA-A\? Best Combo', '⭐ Best Combo'),
    (r'AA Trending', '⚡ Trending'),
    (r'A,\?TA Budget Friendly', '💸 Budget Friendly'),
    (r'A,\?oA\? Attached Itinerary Pipeline', '📍 Attached Itinerary Pipeline'),
    (r'A,\?AA_A,A\? Edit Package', '📝 Edit Package'),
    (r'Entrance Fee \(,\)', 'Entrance Fee (₱)'),
    (r',\$\{spot\.price', '₱${spot.price'),
    (r'o" Create Package', '✨ Create Package'),
]

for pattern, repl in replacements:
    data = re.sub(pattern, repl, data)

# Additional cleanup for known tags
data = re.sub(r'<option value="popular">[^<]*Popular</option>', '<option value="popular">🔥 Popular</option>', data)
data = re.sub(r'<option value="best_combo">[^<]*Best Combo</option>', '<option value="best_combo">⭐ Best Combo</option>', data)
data = re.sub(r'<option value="trending">[^<]*Trending</option>', '<option value="trending">⚡ Trending</option>', data)
data = re.sub(r'<option value="budget">[^<]*Budget Friendly</option>', '<option value="budget">💸 Budget Friendly</option>', data)
data = re.sub(r'<span>[^<]*Attached Itinerary Pipeline</span>', '<span>📍 Attached Itinerary Pipeline</span>', data)
data = re.sub(r'document\.getElementById\(\'formActionHeader\'\)\.innerText = "[^"]*Edit Package";', 'document.getElementById(\'formActionHeader\').innerText = "📝 Edit Package";', data)

data = re.sub(r'Entrance Fee \([^)]*\)', 'Entrance Fee (₱)', data)

# Remove BOM if exists
if data.startswith('\ufeff'):
    data = data[1:]

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(data)
