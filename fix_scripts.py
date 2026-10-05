import re

filepath = 'resources/views/admin/transport.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    data = f.read()

bad_script = """<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
        function confirmDelete() {
            if (confirm('Are you sure you want to remove this vehicle?')) {
                document.getElementById('deleteVehicleForm').submit();
            }
        }
    </script>"""

good_script = """<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>"""

data = data.replace(bad_script, good_script)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(data)
