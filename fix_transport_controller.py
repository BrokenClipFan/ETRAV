filepath = 'app/Http/Controllers/Admin/TransportController.php'
with open(filepath, 'r', encoding='utf-8') as f:
    data = f.read()

destroy_method = """    public function destroy(string $id)
    {
        $vehicle = \\App\\Models\\Transport::findOrFail($id);
        
        if ($vehicle->front_image_path) {
            \\Illuminate\\Support\\Facades\\Storage::disk('public')->delete($vehicle->front_image_path);
        }
        if ($vehicle->side_image_path) {
            \\Illuminate\\Support\\Facades\\Storage::disk('public')->delete($vehicle->side_image_path);
        }
        if ($vehicle->plate_image_path) {
            \\Illuminate\\Support\\Facades\\Storage::disk('public')->delete($vehicle->plate_image_path);
        }

        $vehicle->delete();
        
        return redirect()->route('transport.index')->with('success', 'Vehicle removed successfully.');
    }"""

old_method = """    public function destroy(string $id)
    {
        //
    }"""

data = data.replace(old_method, destroy_method)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(data)
