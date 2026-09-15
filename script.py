import sys
import re

with open('resources/views/bookings.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

def replace_block(match):
    inner = match.group(1)
    
    cancel_match = re.search(r'(@if\([^)]+\)\s*)?<button[^>]*confirmCancel[^>]*>Cancel</button>(\s*@endif)?', inner)
    cancel_btn = cancel_match.group(0) if cancel_match else ''
    
    if not cancel_btn:
        cancel_match2 = re.search(r'<button[^>]*confirmCancel[^>]*>Cancel</button>', inner)
        cancel_btn = cancel_match2.group(0) if cancel_match2 else ''
        
    right_btns = inner.replace(cancel_btn, '').strip()
    
    return f'''<div class="d-flex flex-wrap gap-2 justify-content-between w-100">
                                                <div>
                                                    {cancel_btn.strip()}
                                                </div>
                                                <div class="d-flex flex-wrap gap-2">
                                                    {right_btns}
                                                </div>
                                            </div>'''

content = re.sub(r'<div class="d-flex flex-wrap gap-2 justify-content-end w-100">\s*([\s\S]*?)\s*</div>(?=\s*</div>\s*</div>\s*</div>)', replace_block, content)
content = re.sub(r'<div class="d-flex gap-2">\s*([\s\S]*?)\s*</div>(?=\s*</div>\s*</div>\s*</div>)', replace_block, content)

with open('resources/views/bookings.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
