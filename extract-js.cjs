const fs = require('fs');
let path = 'resources/views/view-package.blade.php';
let text = fs.readFileSync(path, 'utf8');
let scriptMatches = text.match(/<script>([\s\S]*?)<\/script>/g);
if (scriptMatches) {
    let js = scriptMatches[0].replace(/<script>|<\/script>/g, '');
    
    // Stub out the Blade directives so Node can parse it
    js = js.replace(/\{\{.*?\}\}/g, '"blade_string"');
    js = js.replace(/\{!!.*?!!\}/g, '"blade_json"');
    js = js.replace(/@foreach.*?$/gm, '/* foreach */');
    js = js.replace(/@endforeach/g, '/* endforeach */');
    js = js.replace(/@php.*?@endphp/gs, '/* php */');
    js = js.replace(/@if.*?$/gm, '/* if */');
    js = js.replace(/@else.*?$/gm, '/* else */');
    js = js.replace(/@endif/g, '/* endif */');
    
    fs.writeFileSync('test.js', js, 'utf8');
    console.log('done extracting js');
}
