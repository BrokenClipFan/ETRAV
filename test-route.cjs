const fs = require('fs');
let path = 'routes/web.php';
let text = fs.readFileSync(path, 'utf8');

text = text.replace("<?php", "<?php\nRoute::get('/test-render', function() {\n$p = App\\Models\\Package::find(7);\nreturn view('view-package', ['package' => $p, 'packages' => App\\Models\\Package::all(), 'vehicles' => App\\Models\\Transport::where('status', 'active')->get(), 'places' => App\\Models\\Place::all(), 'user' => App\\Models\\User::first(), 'hasNotification' => false, 'bookedDates' => collect()]);\n});");

fs.writeFileSync(path, text, 'utf8');
console.log('done adding test route');
