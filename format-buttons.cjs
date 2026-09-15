const fs = require('fs');
let text = fs.readFileSync('resources/views/bookings.blade.php', 'utf8');

text = text.replace(/<div class="d-flex flex-wrap gap-2 justify-content-end w-100">\s*<button type="button"[\s\S]*?(?:<\/button>\s*@endif|\s*<\/button>)\s*<\/div>/g, (match) => {
    
    let cancelPattern = /(@if\([\s\S]*?Cancel<\/button>\s*@endif|<button type="button"[^>]*confirmCancel[^>]*>Cancel<\/button>)/;
    
    let cancelMatch = match.match(cancelPattern);
    if(!cancelMatch) return match;
    
    let cancelBtn = cancelMatch[0];
    
    let inner = match.replace(/<div class="d-flex flex-wrap gap-2 justify-content-end w-100">/, '');
    inner = inner.replace(/<\/div>$/, '');
    inner = inner.replace(cancelBtn, '');
    
    return '<div class="d-flex flex-wrap gap-2 justify-content-between w-100">\n' +
        '    <div>\n' +
        '        ' + cancelBtn.trim() + '\n' +
        '    </div>\n' +
        '    <div class="d-flex flex-wrap gap-2">\n' +
        '        ' + inner.trim() + '\n' +
        '    </div>\n' +
        '</div>';
});

fs.writeFileSync('resources/views/bookings.blade.php', text);
console.log('done');
