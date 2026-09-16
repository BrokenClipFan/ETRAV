const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');
let scriptMatches = text.match(/<script>([\s\S]*?)<\/script>/g);
if (scriptMatches) {
    let js = scriptMatches[0].replace(/<script>|<\/script>/g, '');
    
    // Remove all blade directives completely to just leave JS structure
    js = js.replace(/\{\{.*?\}\}/g, '0');
    js = js.replace(/\{!!.*?!!\}/g, '""');
    js = js.replace(/@foreach.*?\n/g, '');
    js = js.replace(/@endforeach/g, '');
    js = js.replace(/@php([\s\S]*?)@endphp/g, '');
    js = js.replace(/@if.*?\n/g, '');
    js = js.replace(/@else.*?\n/g, '');
    js = js.replace(/@endif/g, '');
    
    fs.writeFileSync('test.js', js, 'utf8');
    console.log('done extracting js');
}
