const fs = require('fs');
let current = fs.readFileSync('resources/views/view-package.blade.php');

// If there are null bytes, we have to handle them
if (current.includes(Buffer.from([0x00]))) {
    let cleaned = current.toString('utf16le');
    // But some parts are utf8, some are utf16? That's a mess.
}
