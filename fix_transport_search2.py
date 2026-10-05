import re

filepath = 'resources/views/admin/transport.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    data = f.read()

# Fix JS for search filtering to use style.display
old_js = """                    if (title.includes(term) || plate.includes(term)) {
                        card.classList.remove('d-none');
                        card.classList.add('d-flex');
                    } else {
                        card.classList.remove('d-flex');
                        card.classList.add('d-none');
                    }"""

new_js = """                    if (title.includes(term) || plate.includes(term)) {
                        card.classList.remove('d-none');
                    } else {
                        card.classList.add('d-none');
                    }"""
data = data.replace(old_js, new_js)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(data)
