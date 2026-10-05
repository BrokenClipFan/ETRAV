import re

filepath = 'resources/views/welcome.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    data = f.read()

replacement = """        const categoryIcons = {
            @foreach($categories as $cat)
                "{{ strtolower($cat->name) }}": "{{ $cat->icon_path ? asset('storage/' . $cat->icon_path) : '' }}",
            @endforeach
        };

        function createCategoryPinIcon(categoryKey) {
            const key = (categoryKey || '').toLowerCase();
            if (categoryIcons[key]) {
                return L.icon({
                    iconUrl: categoryIcons[key],
                    iconSize: [36, 36],
                    iconAnchor: [18, 36],
                    popupAnchor: [0, -34]
                });
            }

            const config = getCategoryDetails(categoryKey);"""

data = data.replace("""        function createCategoryPinIcon(categoryKey) {
            const config = getCategoryDetails(categoryKey);""", replacement)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(data)
