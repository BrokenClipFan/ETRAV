const fs = require('fs');
let text = fs.readFileSync('resources/views/bookings.blade.php', 'utf8');

text = text.replace(/<script src="https:\/\/cdn\.jsdelivr\.net\/npm\/bootstrap@5\.3\.0\/dist\/js\/bootstrap\.bundle\.min\.js">([\s\S]*?)<\/script>/, '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>\n    <script>$1</script>');

fs.writeFileSync('resources/views/bookings.blade.php', text);
console.log('done');
