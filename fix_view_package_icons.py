import re

filepath = 'resources/views/view-package.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    data = f.read()

replacement = """        const categoryIcons = {
            @foreach($categories ?? [] as $cat)
                "{{ strtolower($cat->name) }}": "{{ $cat->icon_path ? asset('storage/' . $cat->icon_path) : '' }}",
            @endforeach
        };

        function getCategoryDetails(categoryKey) {
            const key = (categoryKey || '').toLowerCase();
            return categoryConfig[key] || {
                icon: 'bi-geo-alt-fill',
                bg: '#0d6efd'
            };
        }

        function createCategoryPinIcon(spot) {
            if (spot && spot.category_icon) {
                return L.icon({
                    iconUrl: spot.category_icon,
                    iconSize: [36, 36],
                    iconAnchor: [18, 36],
                    popupAnchor: [0, -34]
                });
            }
            
            const catName = (spot && spot.category) ? spot.category.toLowerCase() : '';
            if (categoryIcons[catName]) {
                return L.icon({
                    iconUrl: categoryIcons[catName],
                    iconSize: [36, 36],
                    iconAnchor: [18, 36],
                    popupAnchor: [0, -34]
                });
            }
            
            const config = getCategoryDetails(spot ? spot.category : '');
            return L.divIcon({
                className: 'custom-pin-wrapper',
                html: `<div class="custom-category-pin" style="background-color: ${config.bg};"><i class="bi ${config.icon}"></i></div>`,
                iconSize: [36, 36],
                iconAnchor: [18, 36],
                popupAnchor: [0, -34]
            });
        }

        function createGrayCategoryPinIcon(spot) {
            if (spot && spot.category_icon) {
                return L.icon({
                    iconUrl: spot.category_icon,
                    className: 'opacity-75',
                    iconSize: [36, 36],
                    iconAnchor: [18, 36],
                    popupAnchor: [0, -34]
                });
            }
            
            const catName = (spot && spot.category) ? spot.category.toLowerCase() : '';
            if (categoryIcons[catName]) {
                return L.icon({
                    iconUrl: categoryIcons[catName],
                    className: 'opacity-75',
                    iconSize: [36, 36],
                    iconAnchor: [18, 36],
                    popupAnchor: [0, -34]
                });
            }
            
            const config = getCategoryDetails(spot ? spot.category : '');
            return L.divIcon({
                className: 'custom-pin-wrapper',
                html: `<div class="custom-category-pin" style="background-color: ${config.bg}; opacity: 0.9;"><i class="bi ${config.icon}"></i></div>`,
                iconSize: [36, 36],
                iconAnchor: [18, 36],
                popupAnchor: [0, -34]
            });
        }"""

old_block_pattern = r'function getCategoryDetails\(categoryKey\) \{.*?(?=function clearMapLayers\(\) \{)'
data = re.sub(old_block_pattern, replacement + '\n\n        ', data, flags=re.DOTALL)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(data)
