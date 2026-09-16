const fs = require('fs');
let path = 'resources/views/welcome.blade.php';
let text = fs.readFileSync(path, 'utf8');

const regex = /<div class="card package-card-rect package-card shadow-sm bg-white"[\s\S]*?<\/div>\s*<\/div>\s*<\/div>\s*@empty/;

const newCard = `<div class="card package-card-rect package-card border-0 shadow bg-white" 
                            data-package-id="{{ $package->id }}" id="package-card-{{ $package->id }}" 
                            style="border-radius: 1rem !important; overflow: hidden; transition: transform 0.3s ease;">
                            
                            <!-- Top Image Container with Purple Gradient fallback and Heart -->
                            <div class="position-relative d-flex align-items-center justify-content-center text-muted"
                                style="height: 250px; width: 100%; background: linear-gradient(135deg, #7c729b, #938cb5, #b8b3d6);">
                                
                                <!-- Floating Heart Icon -->
                                <button class="btn btn-sm position-absolute top-0 end-0 m-3 rounded-circle d-flex align-items-center justify-content-center border-0" 
                                    style="width: 36px; height: 36px; background: rgba(255,255,255,0.2); backdrop-filter: blur(5px);">
                                    <i class="bi bi-heart text-white fs-6"></i>
                                </button>

                                @if (!empty($package->image_path))
                                    <img src="{{ $package->image_path }}" onerror="this.onerror=null;this.src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22400%22%20height%3D%22300%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20400%20300%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_1%20text%20%7B%20fill%3A%23999%3Bfont-weight%3Anormal%3Bfont-family%3Avar(--bs-font-sans-serif)%2C%20sans-serif%3Bfont-size%3A20pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_1%22%3E%3Crect%20width%3D%22400%22%20height%3D%22300%22%20fill%3D%22%23e2e3e5%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22144%22%20y%3D%22160%22%3ENo%20Image%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E';" alt="{{ $package->name }}" style="object-fit: contain; width: 80%; height: 80%;">
                                @else
                                    <i class="bi bi-image fs-1 opacity-25"></i>
                                @endif
                            </div>

                            <!-- Bottom Content Container -->
                            <div class="p-3 d-flex flex-column flex-grow-1 bg-white">
                                <!-- Title -->
                                <h5 class="fw-bold text-dark mb-2 mt-1 fs-5 text-truncate" style="letter-spacing: -0.5px;" onclick="focusOnPackageRoute({{ $package->id }})">{{ $package->name }}</h5>
                                
                                <!-- Badges / Tags -->
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="border rounded px-2 py-1 text-dark" style="font-size: 11px; font-weight: 600; letter-spacing: 0.5px; border-color: #6c757d !important;">
                                        {{ strtoupper($package->type ?? 'TOUR') }}
                                    </span>
                                    <span class="border rounded px-2 py-1 text-dark" style="font-size: 11px; font-weight: 600; letter-spacing: 0.5px; border-color: #6c757d !important;">
                                        {{ $package->places->count() }} STOPS
                                    </span>
                                    <span class="border rounded px-2 py-1 text-dark pkg-distance-display" style="font-size: 11px; font-weight: 600; letter-spacing: 0.5px; border-color: #6c757d !important;">
                                        EST. ROUTE
                                    </span>
                                </div>
                                
                                <!-- Description -->
                                <p class="text-muted mb-4" style="font-size: 14px; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; color: #4b5563 !important;">
                                    {{ $package->description ?? 'Experience the best of what this package has to offer. A curated journey designed for comfort and flair.' }}
                                </p>

                                <!-- Footer: Price and Button -->
                                <div class="d-flex justify-content-between align-items-end mt-auto pt-2">
                                    <div class="d-flex flex-column">
                                        <span style="font-size: 10px; font-weight: 800; letter-spacing: 1px; color: #374151;">PRICE</span>
                                        <span class="fs-4 fw-bold text-dark est-price-display" style="letter-spacing: -1px; line-height: 1;">&#8369; --</span>
                                    </div>
                                    
                                    <a href="/package/{{ $package->id }}" class="btn text-white rounded-3 px-4 py-2 shadow-sm" style="background-color: #5c598c; font-weight: 500; font-size: 14px; letter-spacing: 0.3px;">
                                        Book Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty`;

text = text.replace(regex, newCard);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing card');
