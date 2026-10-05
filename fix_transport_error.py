import re

filepath = 'app/Http/Controllers/Admin/TransportController.php'
with open(filepath, 'r', encoding='utf-8') as f:
    data = f.read()

data = data.replace(
    "return back()->withErrors('error', 'plate number is already registered');",
    "return back()->withErrors(['plate_number' => 'plate number is already registered']);"
)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(data)
