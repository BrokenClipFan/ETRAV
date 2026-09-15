const fs = require('fs');
let text = fs.readFileSync('resources/views/view-package.blade.php', 'utf8');

// Update startPickupMapMapping
let oldStart = `        function startPickupMapMapping() {
            if (bsModalInstance) {
                bsModalInstance.hide();
            }
            pickupMappingModeActive = true;
            document.getElementById('mapPickerInstruction').classList.remove('d-none');
            document.getElementById('mapPickerInstruction').classList.add('d-flex');
        }`;

let newStart = `        function startPickupMapMapping() {
            if (bsModalInstance) {
                bsModalInstance.hide();
            }
            pickupMappingModeActive = true;
            document.getElementById('mapPickerInstruction').classList.remove('d-none');
            document.getElementById('mapPickerInstruction').classList.add('d-flex');

            // Add backdrop overlay
            let backdrop = document.getElementById('mapFocusBackdrop');
            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.id = 'mapFocusBackdrop';
                backdrop.style.cssText = 'position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.75); z-index: 1040; transition: opacity 0.3s;';
                document.body.appendChild(backdrop);
            }
            backdrop.style.display = 'block';

            // Elevate map container wrapper above the backdrop
            const mapCol = document.querySelector('.map-container').parentElement;
            mapCol.style.position = 'relative';
            mapCol.style.zIndex = '1050';
            
            // Add a subtle glow/shadow to the map wrapper
            mapCol.classList.add('shadow-lg');
            mapCol.style.boxShadow = '0 0 40px rgba(0,0,0,0.5)';
        }`;

text = text.replace(oldStart, newStart);


// Update the setTimeout inside setPickupCoordinates
let oldEnd = `            setTimeout(() => {
                pickupMappingModeActive = false;
                document.getElementById('mapPickerInstruction').classList.add('d-none');
                document.getElementById('mapPickerInstruction').classList.remove('d-flex');
                if (bsModalInstance) {
                    bsModalInstance.show();
                }`;

let newEnd = `            setTimeout(() => {
                pickupMappingModeActive = false;
                document.getElementById('mapPickerInstruction').classList.add('d-none');
                document.getElementById('mapPickerInstruction').classList.remove('d-flex');

                // Hide backdrop and reset map container
                let backdrop = document.getElementById('mapFocusBackdrop');
                if (backdrop) backdrop.style.display = 'none';

                const mapCol = document.querySelector('.map-container').parentElement;
                mapCol.style.zIndex = '';
                mapCol.classList.remove('shadow-lg');
                mapCol.style.boxShadow = '';

                if (bsModalInstance) {
                    bsModalInstance.show();
                }`;

text = text.replace(oldEnd, newEnd);

fs.writeFileSync('resources/views/view-package.blade.php', text);
console.log('done');
