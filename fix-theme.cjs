const fs = require('fs');
let path = 'resources/views/welcome.blade.php';
let text = fs.readFileSync(path, 'utf8');

// Replace purple gradient with a blue gradient that matches bootstrap primary
text = text.replace(/linear-gradient\(135deg, #7c729b, #938cb5, #b8b3d6\)/g, 'linear-gradient(135deg, #084298, #0d6efd, #6ea8fe)');

// Replace Book Now button color
text = text.replace(/style="background-color: #5c598c; font-weight: 500; font-size: 14px; letter-spacing: 0\.3px;"/g, 'style="font-weight: 500; font-size: 14px; letter-spacing: 0.3px;"');
text = text.replace(/class="btn text-white rounded-3 px-4 py-2 shadow-sm"/g, 'class="btn btn-primary rounded-3 px-4 py-2 shadow-sm"');

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing theme colors');
