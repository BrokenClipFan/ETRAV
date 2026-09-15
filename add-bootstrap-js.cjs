const fs = require('fs');
let viewPath = 'resources/views/admin/booking-details.blade.php';
let text = fs.readFileSync(viewPath, 'utf8');

if (!text.includes('bootstrap.bundle.min.js')) {
    text = text.replace(/<\/body>/, '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>\n</body>');
    fs.writeFileSync(viewPath, text);
}
console.log('done');
