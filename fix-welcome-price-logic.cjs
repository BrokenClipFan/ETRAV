const fs = require('fs');
let path = 'resources/views/welcome.blade.php';
let text = fs.readFileSync(path, 'utf8');

let oldLogic = `                    const pkgPrice = (packageData[pkgId] && packageData[pkgId].package_price) ? parseFloat(packageData[pkgId].package_price) : 0;
                    
                    let pMin = (parseFloat(minVehicle.base_price) + pkgPrice).toLocaleString('en-US', {minimumFractionDigits: 0});
                    let pMax = (parseFloat(maxVehicle.base_price) + pkgPrice).toLocaleString('en-US', {minimumFractionDigits: 0});
                    let rangeText = \`&#8369;\${pMin} - &#8369;\${pMax}\`;
                    const estDisplay = card.querySelector('.est-price-display');
                    const distDisplay = card.querySelector('.pkg-distance-display');
                    
                    let kmStr = "";
                    if (typeof packageData !== 'undefined' && packageData[pkgId] && packageData[pkgId].spots && packageData[pkgId].spots.length > 1) {
                        try {
                            let totalDist = 0;
                            const spots = packageData[pkgId].spots;
                            for(let i=0; i<spots.length-1; i++) {
                                const p1 = L.latLng(spots[i].lat, spots[i].lng);
                                const p2 = L.latLng(spots[i+1].lat, spots[i+1].lng);
                                totalDist += p1.distanceTo(p2);
                            }
                            let km = (totalDist * 1.3) / 1000;
                            kmStr = \` &bull; \${km.toFixed(1)} km\`;
                            if(distDisplay) distDisplay.innerHTML = \`Est. Route: \${km.toFixed(1)} km\`;
                        } catch (e) {
                            if(distDisplay) distDisplay.innerHTML = \`Route dist err\`;
                        }
                    } else {
                        if(distDisplay) distDisplay.innerHTML = \`Single Destination\`;
                    }
                    
                    if(estDisplay) {
                        estDisplay.innerHTML = rangeText + kmStr;
                    }`;

let newLogic = `                    const pkgPrice = (packageData[pkgId] && packageData[pkgId].package_price) ? parseFloat(packageData[pkgId].package_price) : 0;
                    
                    let pDisplay = pkgPrice.toLocaleString('en-US', {minimumFractionDigits: 2});
                    let priceText = \`&#8369;\${pDisplay}\`;
                    const estDisplay = card.querySelector('.est-price-display');
                    const distDisplay = card.querySelector('.pkg-distance-display');
                    
                    if (typeof packageData !== 'undefined' && packageData[pkgId] && packageData[pkgId].spots && packageData[pkgId].spots.length > 1) {
                        try {
                            let totalDist = 0;
                            const spots = packageData[pkgId].spots;
                            for(let i=0; i<spots.length-1; i++) {
                                const p1 = L.latLng(spots[i].lat, spots[i].lng);
                                const p2 = L.latLng(spots[i+1].lat, spots[i+1].lng);
                                totalDist += p1.distanceTo(p2);
                            }
                            let km = (totalDist * 1.3) / 1000;
                            if(distDisplay) distDisplay.innerHTML = \`Est. Route: \${km.toFixed(1)} km\`;
                        } catch (e) {
                            if(distDisplay) distDisplay.innerHTML = \`Route dist err\`;
                        }
                    } else {
                        if(distDisplay) distDisplay.innerHTML = \`Single Destination\`;
                    }
                    
                    if(estDisplay) {
                        estDisplay.innerHTML = priceText;
                    }`;

text = text.replace(oldLogic, newLogic);

fs.writeFileSync(path, text, 'utf8');
console.log('done fixing price logic');
