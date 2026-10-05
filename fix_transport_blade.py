import re

filepath = 'resources/views/admin/transport.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    data = f.read()

# 1. Add Delete Form
delete_form_html = """
    <!-- Hidden Delete Form -->
    <form id="deleteVehicleForm" method="POST" action="" class="d-none">
        @csrf
        @method('DELETE')
    </form>
"""
if 'id="deleteVehicleForm"' not in data:
    data = data.replace('</body>', delete_form_html + '\n</body>')

# 2. Add Delete Button
old_save_buttons = """<div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill fw-medium shadow-sm"><i
                                class="bi bi-save me-1"></i> Save Vehicle Data</button>
                    </div>"""

new_save_buttons = """<div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill fw-medium shadow-sm"><i
                                class="bi bi-save me-1"></i> Save Vehicle Data</button>
                        <button type="button" class="btn btn-outline-danger rounded-pill fw-medium shadow-sm d-none" id="deleteVehicleBtn" onclick="confirmDelete()"><i class="bi bi-trash me-1"></i> Remove Vehicle</button>
                    </div>"""
data = data.replace(old_save_buttons, new_save_buttons)

# 3. Remove Maintenance Logs block
maintenance_pattern = r'<div class="bg-white rounded-4 p-4 shadow-sm border mt-4">\s*<h6 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Recent\s*Maintenance Logs</h6>\s*<div class="text-center py-3 text-muted small font-italic">\s*No logs recorded for this vehicle yet\.\s*</div>\s*</div>'
data = re.sub(maintenance_pattern, '', data, flags=re.DOTALL)

# 4. JS functions update
js_img_script = """
            if (vehicle.front_image_path) {
                frontView.innerHTML =
                    `<img src="${assetBaseUrl}${vehicle.front_image_path}" class="w-100 h-100" style="object-fit: cover;">`;
                frontView.classList.remove('p-5');
            } else {
                frontView.classList.add('p-5');
            }
            if (vehicle.side_image_path) {
                sideView.innerHTML =
                    `<img src="${assetBaseUrl}${vehicle.side_image_path}" class="w-100 h-100" style="object-fit: cover;">`;
                sideView.classList.remove('p-5');
            } else {
                sideView.classList.add('p-5');
            }
            if (vehicle.plate_image_path) {
                plateView.innerHTML =
                    `<img src="${assetBaseUrl}${vehicle.plate_image_path}" class="w-100 h-100" style="object-fit: cover;">`;
                plateView.classList.remove('p-5');
            } else {
                plateView.classList.add('p-5');
            }

            // Remove upload previews from the form
            document.querySelectorAll('.vehicle-img-preview').forEach(img => img.remove());
            
            // Show Delete button
            const delBtn = document.getElementById('deleteVehicleBtn');
            if(delBtn) {
                delBtn.classList.remove('d-none');
                document.getElementById('deleteVehicleForm').action = defaultAction.replace(/\\/$/, '') + '/' + vehicle.id;
            }
        }"""

old_img_script = r"""            if \(vehicle\.front_image_path\) \{[\s\S]*?\}
            if \(vehicle\.side_image_path\) \{[\s\S]*?\}
            if \(vehicle\.plate_image_path\) \{[\s\S]*?\}

            // Remove upload previews from the form
            document\.querySelectorAll\('\.vehicle-img-preview'\)\.forEach\(img => img\.remove\(\)\);
        \}"""
data = re.sub(old_img_script, js_img_script.strip(), data, flags=re.DOTALL)

js_reset_add = """            // Hide Delete button
            const delBtn = document.getElementById('deleteVehicleBtn');
            if(delBtn) delBtn.classList.add('d-none');
"""
data = data.replace("document.getElementById('vehicleId').value = '';", "document.getElementById('vehicleId').value = '';\n" + js_reset_add)

# 5. Add confirmDelete function
confirm_func = """
        function confirmDelete() {
            if (confirm('Are you sure you want to remove this vehicle?')) {
                document.getElementById('deleteVehicleForm').submit();
            }
        }
    </script>
"""
data = data.replace('</script>', confirm_func)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(data)
