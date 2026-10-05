import re

filepath = 'app/Http/Controllers/BookingController.php'
with open(filepath, 'r', encoding='utf-8') as f:
    data = f.read()

# Add categories to BookingController@index
data = data.replace("$packages = Package::with('places')->get();", "$packages = Package::with('places')->get();\n        $categories = \\App\\Models\\Category::all();")
data = data.replace("return view('welcome', compact('packages', 'places', 'user', 'vehicles', 'hasNotification', 'bookedDates'));", "return view('welcome', compact('packages', 'places', 'user', 'vehicles', 'hasNotification', 'bookedDates', 'categories'));")

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(data)
