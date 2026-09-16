const fs = require('fs');
let path = 'resources/views/welcome.blade.php';
let text = fs.readFileSync(path, 'utf8');

// Add cursor and onclick to the main card
let oldCardDiv = `id="package-card-{{ $package->id }}" 
                            style="border-radius: 1rem !important; overflow: hidden; transition: transform 0.3s ease;">`;
let newCardDiv = `id="package-card-{{ $package->id }}" 
                            onclick="filterSidebarByPackage({{ $package->id }})"
                            style="border-radius: 1rem !important; overflow: hidden; transition: transform 0.3s ease; cursor: pointer;">`;

text = text.replace(oldCardDiv, newCardDiv);

// Remove it from the title
let oldTitle = `onclick="focusOnPackageRoute({{ $package->id }})">{{ $package->name }}</h5>`;
let newTitle = `>{{ $package->name }}</h5>`;
text = text.replace(oldTitle, newTitle);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing card onclick');
