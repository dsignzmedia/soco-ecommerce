<?php

namespace Database\Seeders;

use App\Models\Admin\Master\ProductMapping;
use App\Models\Admin\Master\School;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schools = School::where('status', 'active')->get();
        
        $grades = [
            'LKG', 'UKG', 
            '1', '2', '3', '4', '5', 
            '6', '7', '8', '9', '10', 
            '11', '12'
        ];

        $categories = ['regular_uniforms', 'sports', 'fabrics'];
        $productTypes = ['authorized', 'merchandised'];

        foreach ($schools as $school) {
            foreach ($grades as $grade) {
                foreach ($categories as $category) {
                    foreach ($productTypes as $type) {
                        // Create 2 products for each combination
                        for ($i = 1; $i <= 2; $i++) {
                            // Determine product name based on category
                            $baseName = match($category) {
                                'regular_uniforms' => $i == 1 ? 'School Shirt' : 'School Trousers',
                                'sports' => $i == 1 ? 'PE T-Shirt' : 'PE Shorts',
                                'fabrics' => $i == 1 ? 'Uniform Fabric (Shirting)' : 'Uniform Fabric (Suiting)',
                                default => 'Product'
                            };

                            // Randomly select one of the 8 images
                            $imageName = 'Image' . rand(1, 8) . '.png';
                            
                            // Copy the asset to storage so it works with the storage link
                            $sourcePath = public_path('assets/img/product_images/' . $imageName);
                            $storagePath = 'products/' . $imageName;
                            
                            if (file_exists($sourcePath)) {
                                if (!file_exists(storage_path('app/public/products'))) {
                                    mkdir(storage_path('app/public/products'), 0755, true);
                                }
                                copy($sourcePath, storage_path('app/public/' . $storagePath));
                            }

                            // Random gender
                            $gender = ['Boys', 'Girls', 'Unisex'][rand(0, 2)];

                            ProductMapping::create([
                                'school_id' => $school->id,
                                'grade' => $grade,
                                'product_name' => $school->name . ' ' . $baseName . ' ' . $i,
                                'category' => $category,
                                'product_type' => $type,
                                'gender' => $gender,
                                'price_sale' => rand(300, 1000),
                                'inventory_stock' => rand(20, 100),
                                'low_stock_threshold' => 10,
                                'status' => 'live',
                                'description' => "High quality " . strtolower($baseName) . " for " . $school->name . " students. Durable and comfortable.",
                                'featured_image' => $storagePath, 
                            ]);
                        }
                    }
                }
            }
        }
    }
}
