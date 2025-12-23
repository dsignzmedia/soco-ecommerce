<?php

namespace Database\Seeders;

use App\Models\Admin\Master\ProductType;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productTypes = [
            [
                'name' => 'Authorized Product',
                'slug' => 'authorized',
                'description' => 'Products authorized by the school',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Optional Product',
                'slug' => 'optional',
                'description' => 'Optional products available for purchase',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Back to School',
                'slug' => 'back_to_school',
                'description' => 'Back to school products and essentials',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Merchandise',
                'slug' => 'merchandised',
                'description' => 'School merchandise and branded items',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($productTypes as $type) {
            ProductType::firstOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }
}
