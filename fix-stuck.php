<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\Transport::where('status', 'unavailable')->update(['status' => 'active']);
echo "Updated stuck vehicles to active\n";
