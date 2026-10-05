import re

filepath = 'app/Http/Controllers/Admin/TransportController.php'
with open(filepath, 'r', encoding='utf-8') as f:
    data = f.read()

# Remove the base_price injection since it doesn't exist in the DB!
data = re.sub(r'\s*\$validated\[\'base_price\'\] = 0; // Legacy required field\n', '\n', data)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(data)
