import re

filepath = 'resources/views/admin/packages.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    data = f.read()

# Replace editSpotCategory select
edit_dropdown = """<div class="dropdown w-100">
                                <input type="hidden" name="category" id="editSpotCategory" required>
                                <button class="btn border w-100 text-start d-flex justify-content-between align-items-center bg-white text-dark form-select spot-modal-input" type="button" id="editSpotCategoryBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span id="editSpotCategoryLabel" class="text-muted">Select category...</span>
                                </button>
                                <ul class="dropdown-menu w-100 shadow-sm border-0" aria-labelledby="editSpotCategoryBtn" style="max-height: 200px; overflow-y: auto;">
                                    @foreach($categories as $cat)
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center py-2" href="#" onclick="event.preventDefault(); selectCategory('edit', '{{ $cat->name }}', '{{ $cat->icon_path ? asset('storage/' . $cat->icon_path) : '' }}')">
                                                @if($cat->icon_path)
                                                    <img src="{{ asset('storage/' . $cat->icon_path) }}" class="me-2 rounded" style="width: 20px; height: 20px; object-fit: contain;">
                                                @else
                                                    <i class="bi bi-geo-alt-fill text-secondary me-2"></i>
                                                @endif
                                                {{ $cat->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>"""
data = re.sub(r'<select name="category" id="editSpotCategory"[^>]*>.*?</select>', edit_dropdown, data, flags=re.DOTALL)


# Replace newSpotCategory select
new_dropdown = """<div class="dropdown w-100">
                            <input type="hidden" id="newSpotCategory" required>
                            <button class="btn border w-100 text-start d-flex justify-content-between align-items-center bg-white text-dark form-select spot-modal-input" type="button" id="newSpotCategoryBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                <span id="newSpotCategoryLabel" class="text-muted">Select category...</span>
                            </button>
                            <ul class="dropdown-menu w-100 shadow-sm border-0" aria-labelledby="newSpotCategoryBtn" style="max-height: 200px; overflow-y: auto;">
                                @foreach($categories as $cat)
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center py-2" href="#" onclick="event.preventDefault(); selectCategory('new', '{{ $cat->name }}', '{{ $cat->icon_path ? asset('storage/' . $cat->icon_path) : '' }}')">
                                            @if($cat->icon_path)
                                                <img src="{{ asset('storage/' . $cat->icon_path) }}" class="me-2 rounded" style="width: 20px; height: 20px; object-fit: contain;">
                                            @else
                                                <i class="bi bi-geo-alt-fill text-secondary me-2"></i>
                                            @endif
                                            {{ $cat->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>"""
data = re.sub(r'<select id="newSpotCategory"[^>]*>.*?</select>', new_dropdown, data, flags=re.DOTALL)


# Inject JS function selectCategory and modify openEditSpotModal
js_injection = """
        function selectCategory(type, name, iconUrl) {
            const inputId = type === 'edit' ? 'editSpotCategory' : 'newSpotCategory';
            const labelId = type === 'edit' ? 'editSpotCategoryLabel' : 'newSpotCategoryLabel';
            
            document.getElementById(inputId).value = name;
            
            const labelEl = document.getElementById(labelId);
            labelEl.classList.remove('text-muted');
            if (iconUrl) {
                labelEl.innerHTML = `<img src="${iconUrl}" class="me-2 rounded" style="width: 20px; height: 20px; object-fit: contain;"> ${name}`;
            } else {
                labelEl.innerHTML = `<i class="bi bi-geo-alt-fill text-secondary me-2"></i> ${name}`;
            }
        }
        
        function openEditSpotModal"""

data = data.replace('function openEditSpotModal', js_injection)

# Modify openEditSpotModal to update the custom dropdown label
old_edit_js = "document.getElementById('editSpotCategory').value = (spot.category || '');"
new_edit_js = """const catName = (spot.category || '');
            const lowerCatName = catName.toLowerCase();
            const iconUrl = categoryIcons[lowerCatName] || '';
            if(catName) {
                selectCategory('edit', catName, iconUrl);
            } else {
                document.getElementById('editSpotCategory').value = '';
                document.getElementById('editSpotCategoryLabel').innerHTML = 'Select category...';
                document.getElementById('editSpotCategoryLabel').classList.add('text-muted');
            }"""

data = data.replace(old_edit_js, new_edit_js)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(data)
