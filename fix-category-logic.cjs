const fs = require('fs');
let path = 'resources/views/welcome.blade.php';
let text = fs.readFileSync(path, 'utf8');

let oldLogic = `$category = $package->category ?? 'Standard';`;
let newLogic = `$type = $package->type ?? 'Standard';`;

text = text.replace(oldLogic, newLogic);
text = text.replace(/strtolower\(\$category\)/g, 'strtolower($type)');
text = text.replace(/{{ \$category }}/g, '{{ $type }}');

// Now update the badge HTML
let oldBadge = `<span class="border rounded px-2 py-1 text-dark" style="font-size: 11px; font-weight: 600; letter-spacing: 0.5px; border-color: #6c757d !important;">
                                        {{ strtoupper($package->type ?? 'TOUR') }}
                                    </span>`;
let newBadge = `<span class="rounded px-2 py-1 {{ $badgeStyles }} d-flex align-items-center gap-1 shadow-sm" style="font-size: 11px; font-weight: 600; letter-spacing: 0.5px; border: none !important;">
                                        <i class="bi {{ $tagIcon }}"></i> {{ strtoupper($type) }}
                                    </span>`;

text = text.replace(oldBadge, newBadge);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing logic');
