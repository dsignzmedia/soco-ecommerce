<?php

namespace Database\Seeders;

use App\Models\Admin\Master\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schools = [
            [
                'name' => 'Stanes ICSE School',
                'city' => 'Peelamedu',
                'status' => 'active',
                'logo_file' => 'Stanes ICSE logo.png',
            ],
            [
                'name' => 'Stanes School CBSE',
                'city' => 'Avinashi Road',
                'status' => 'active',
                'logo_file' => 'Stanes School CBSE logo.jpg',
            ],
            [
                'name' => 'Stanes Anglo Indian Higher Secondary School (AIHSS) – Samacheer',
                'city' => 'Avinashi Road',
                'status' => 'active',
                'logo_file' => 'Stanes Anglo Indian Higher Secondary School (AIHSS) – Samacheer logo.png',
            ],
            [
                'name' => 'Bharatiya Vidya Bhavan Matric Higher Secondary School (BVB) – RS Puram',
                'city' => 'R S Puram',
                'status' => 'active',
                'logo_file' => 'Bharatiya Vidya Bhavan Matric Higher Secondary School (BVB) – RS Puram logo.jpg',
            ],
            [
                'name' => 'Bharatiya Vidya Bhavan Matric Higher Secondary School (BVB) – Ajjanur',
                'city' => 'Ajjanur',
                'status' => 'active',
                'logo_file' => 'Bharatiya Vidya Bhavan Matric Higher Secondary School (BVB) – Ajjanur logo.jpg',
            ],
        ];

        foreach ($schools as $schoolData) {
            $schoolData['slug'] = \Illuminate\Support\Str::slug($schoolData['name']);
            
            // Handle Logo
            if (isset($schoolData['logo_file'])) {
                $sourcePath = public_path('assets/img/school_logo/' . $schoolData['logo_file']);
                $storagePath = 'schools/' . $schoolData['logo_file'];
                
                if (file_exists($sourcePath)) {
                    if (!file_exists(storage_path('app/public/schools'))) {
                        mkdir(storage_path('app/public/schools'), 0755, true);
                    }
                    copy($sourcePath, storage_path('app/public/' . $storagePath));
                    $schoolData['logo'] = $storagePath;
                }
                unset($schoolData['logo_file']); // Remove from array before creating model
            }

            School::updateOrCreate(
                ['name' => $schoolData['name']],
                $schoolData
            );
        }
    }
}
