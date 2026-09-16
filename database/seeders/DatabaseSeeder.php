<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Place;
use App\Models\Package;
use App\Models\Transport;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin User
        User::updateOrCreate(
            ['email' => 'admin@etrav.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'is_admin' => 1
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@etrav.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
                'is_admin' => 0
            ]
        );

        // 2. Create Transports (Vehicles)
        $vehicles = [
            [
                'brand' => 'Toyota',
                'model' => 'Hiace Commuter',
                'plate_number' => 'ABC-1234',
                'capacity' => 14,
                'base_price' => 3500,
                'interval_rate' => 1000,
                'pricing_distance' => 10000,
                'status' => 'active',
                'front_image_path' => 'vehicles/hiace-front.jpg',
                'side_image_path' => 'vehicles/hiace-side.jpg',
                'plate_image_path' => 'vehicles/hiace-plate.jpg',
            ],
            [
                'brand' => 'Toyota',
                'model' => 'Innova',
                'plate_number' => 'XYZ-9876',
                'capacity' => 6,
                'base_price' => 2500,
                'interval_rate' => 500,
                'pricing_distance' => 10000,
                'status' => 'active',
                'front_image_path' => 'vehicles/innova-front.jpg',
                'side_image_path' => 'vehicles/innova-side.jpg',
                'plate_image_path' => 'vehicles/innova-plate.jpg',
            ],
            [
                'brand' => 'Ford',
                'model' => 'Transit',
                'plate_number' => 'LMN-4567',
                'capacity' => 12,
                'base_price' => 4000,
                'interval_rate' => 1200,
                'pricing_distance' => 10000,
                'status' => 'active',
                'front_image_path' => 'vehicles/transit-front.jpg',
                'side_image_path' => 'vehicles/transit-side.jpg',
                'plate_image_path' => 'vehicles/transit-plate.jpg',
            ]
        ];

        foreach ($vehicles as $v) {
            Transport::updateOrCreate(['plate_number' => $v['plate_number']], $v);
        }

        // 3. Create Places (Cebu)
        $places = [
            [
                'name' => 'Magellan\'s Cross',
                'price' => 0,
                'category' => 'Historical',
                'place' => 'Cebu City',
                'description' => 'A Christian cross planted by Portuguese and Spanish explorers as ordered by Ferdinand Magellan upon arriving in Cebu in 1521.',
                'image_path' => 'places/magellan.jpg',
                'latitude' => 10.2936,
                'longitude' => 123.9022,
            ],
            [
                'name' => 'Basilica Minore del Santo Nino',
                'price' => 0,
                'category' => 'Historical',
                'place' => 'Cebu City',
                'description' => 'The oldest Roman Catholic church in the country, built on the spot where the image of the Santo Nino de Cebu was found.',
                'image_path' => 'places/basilica.jpg',
                'latitude' => 10.2942,
                'longitude' => 123.9021,
            ],
            [
                'name' => 'Fort San Pedro',
                'price' => 30,
                'category' => 'Historical',
                'place' => 'Cebu City',
                'description' => 'A military defense structure in Cebu, built by the Spanish under the command of Miguel Lopez de Legazpi.',
                'image_path' => 'places/fortsanpedro.jpg',
                'latitude' => 10.2925,
                'longitude' => 123.9058,
            ],
            [
                'name' => 'Temple of Leah',
                'price' => 100,
                'category' => 'Attraction',
                'place' => 'Cebu City',
                'description' => 'A grand Roman-style temple built as a symbol of a husband\'s undying love for his wife.',
                'image_path' => 'places/templeofleah.jpg',
                'latitude' => 10.3683,
                'longitude' => 123.8785,
            ],
            [
                'name' => 'Sirao Flower Garden',
                'price' => 100,
                'category' => 'Nature',
                'place' => 'Cebu City',
                'description' => 'Known as the "Little Amsterdam of Cebu" famous for its beautiful celosia flowers and hilltop views.',
                'image_path' => 'places/sirao.jpg',
                'latitude' => 10.4069,
                'longitude' => 123.8741,
            ],
            [
                'name' => 'Cebu Taoist Temple',
                'price' => 0,
                'category' => 'Culture',
                'place' => 'Cebu City',
                'description' => 'A large and colorful Taoist temple built in 1972 by Cebu\'s substantial Chinese community.',
                'image_path' => 'places/taoist.jpg',
                'latitude' => 10.3344,
                'longitude' => 123.8858,
            ]
        ];

        foreach ($places as $p) {
            Place::updateOrCreate(['name' => $p['name']], $p);
        }

        // 4. Create Packages & Link Places
        $packages = [
            [
                'name' => 'Cebu Historical Tour',
                'type' => 'Day Tour',
                'description' => 'Discover the rich history of the oldest city in the Philippines. Visit centuries-old churches, forts, and monuments.',
                'image_path' => 'packages/cebu-historical.jpg',
                'places' => ['Magellan\'s Cross', 'Basilica Minore del Santo Nino', 'Fort San Pedro']
            ],
            [
                'name' => 'Cebu Highlands Tour',
                'type' => 'Day Tour',
                'description' => 'Escape the city heat and enjoy the breathtaking views and beautiful gardens up in the mountains of Cebu.',
                'image_path' => 'packages/cebu-highlands.jpg',
                'places' => ['Temple of Leah', 'Sirao Flower Garden', 'Cebu Taoist Temple']
            ],
            [
                'name' => 'The Ultimate Cebu Experience',
                'type' => 'Multi-Day',
                'description' => 'Experience the perfect blend of Cebu\'s deep historical roots and its stunning mountain scenery in one amazing trip.',
                'image_path' => 'packages/cebu-ultimate.jpg',
                'places' => ['Magellan\'s Cross', 'Fort San Pedro', 'Temple of Leah', 'Sirao Flower Garden']
            ]
        ];

        // Clean tables to avoid duplicates when running multiple times
        DB::table('package_places')->truncate();
        
        // Remove old Manila places/packages if they exist
        Place::whereIn('name', ['Rizal Park', 'Intramuros', 'Manila Ocean Park', 'National Museum of Fine Arts', 'Binondo Chinatown'])->delete();
        Package::whereIn('name', ['Historical Manila Tour', 'Manila City Leisure Day', 'The Ultimate Manila Experience'])->delete();

        foreach ($packages as $pkgData) {
            $placesList = $pkgData['places'];
            unset($pkgData['places']);

            $package = Package::updateOrCreate(['name' => $pkgData['name']], $pkgData);

            $pos = 1;
            foreach($placesList as $pName) {
                $pid = Place::where('name', $pName)->value('id');
                if ($pid) {
                    DB::table('package_places')->insert([
                        'package_id' => $package->id,
                        'place_id' => $pid,
                        'position' => $pos++,
                        'duration' => '01:00:00', // 1 hour duration format
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }
    }
}
