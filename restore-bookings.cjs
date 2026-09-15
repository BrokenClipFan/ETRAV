const fs = require('fs');
let viewPath = 'resources/views/bookings.blade.php';
let text = fs.readFileSync(viewPath, 'utf8');

// 1. Add mb-4 to cards
text = text.replace(/class="card shadow-sm/g, 'class="card mb-4 shadow-sm');

// 2. Fix the nested script tag bug
text = text.replace(/<\/script>\n    <script>\n        function confirmCancel/g, "</script>\n    <script>\n        function confirmCancel"); // Wait, let's just make sure the confirmCancel script is correctly formatted.
// Actually, in the base file from git, how is the cancelModal script injected?
