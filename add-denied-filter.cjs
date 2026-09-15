const fs = require('fs');
let viewPath = 'resources/views/admin/bookings.blade.php';
let text = fs.readFileSync(viewPath, 'utf8');

let oldTabs = `<button class="btn btn-sm btn-light text-muted rounded-2 px-3 filter-btn" data-filter="completed">Completed</button>
                <button class="btn btn-sm btn-light text-muted rounded-2 px-3 filter-btn" data-filter="all">All Bookings</button>`;

let newTabs = `<button class="btn btn-sm btn-light text-muted rounded-2 px-3 filter-btn" data-filter="completed">Completed</button>
                <button class="btn btn-sm btn-light text-muted rounded-2 px-3 filter-btn" data-filter="cancelled">Denied</button>
                <button class="btn btn-sm btn-light text-muted rounded-2 px-3 filter-btn" data-filter="all">All Bookings</button>`;

text = text.replace(oldTabs, newTabs);

fs.writeFileSync(viewPath, text);
console.log('done updating admin tabs');
