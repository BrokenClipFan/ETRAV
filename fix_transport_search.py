import re

filepath = 'resources/views/admin/transport.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    data = f.read()

# Add ID to search input
old_search_html = """<input type="text" class="form-control" placeholder="Search vehicle...">"""
new_search_html = """<input type="text" class="form-control" id="vehicleSearchInput" placeholder="Search vehicle...">"""
data = data.replace(old_search_html, new_search_html)

# Add JS for search filtering
js_search_logic = """
        // Vehicle Search functionality
        const searchInput = document.getElementById('vehicleSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                document.querySelectorAll('.vehicle-item-card').forEach(card => {
                    const title = card.querySelector('h6')?.innerText.toLowerCase() || '';
                    const plate = card.querySelector('.font-monospace')?.innerText.toLowerCase() || '';
                    
                    if (title.includes(term) || plate.includes(term)) {
                        card.classList.remove('d-none');
                        card.classList.add('d-flex');
                    } else {
                        card.classList.remove('d-flex');
                        card.classList.add('d-none');
                    }
                });
            });
        }
"""
data = data.replace('// Initialize as create mode', js_search_logic + '\n        // Initialize as create mode')

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(data)
