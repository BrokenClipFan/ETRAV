const fs = require('fs');
let text = fs.readFileSync('resources/views/bookings.blade.php', 'utf8');

text = text.replace(/<div>\s*<\/div>\s*<div class="d-flex flex-wrap gap-2">([\s\S]*?)<\/div>/g, (match, inner) => {
    let output = inner;
    let cancelBtn = '';
    
    let parts = output.split('@if(in_array(->status, [\\'pending\\', \\'approved\\', \\'confirmed\\']))');
    if (parts.length > 1) {
        cancelBtn = '@if(in_array(->status, [\\'pending\\', \\'approved\\', \\'confirmed\\']))' + parts[1].split('@endif')[0] + '@endif';
        output = parts[0] + parts[1].substring(parts[1].indexOf('@endif') + 6);
    } else {
        let parts2 = output.split('<button type="button" class="btn btn-outline-danger');
        if (parts2.length > 1) {
            cancelBtn = '<button type="button" class="btn btn-outline-danger' + parts2[1].split('</button>')[0] + '</button>';
            output = parts2[0] + parts2[1].substring(parts2[1].indexOf('</button>') + 9);
        }
    }
    
    return \<div>
    \
</div>
<div class="d-flex flex-wrap gap-2">
    \
</div>\;
});

fs.writeFileSync('resources/views/bookings.blade.php', text);
