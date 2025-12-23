<?php

namespace Database\Seeders;

use App\Models\Admin\Master\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Master Admin Categories
            [
                'name' => 'Regular Uniforms',
                'slug' => 'regular_uniforms',
                'description' => 'Standard school uniforms',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'House Uniforms',
                'slug' => 'house_uniforms',
                'description' => 'House-specific uniforms',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Sports Uniforms',
                'slug' => 'sports',
                'description' => 'Sports and physical education uniforms',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Shoes',
                'slug' => 'shoes',
                'description' => 'School shoes and footwear',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Belts',
                'slug' => 'belts',
                'description' => 'School belts',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Socks',
                'slug' => 'socks',
                'description' => 'School socks',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Ties',
                'slug' => 'ties',
                'description' => 'School ties',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Fabrics',
                'slug' => 'fabrics',
                'description' => 'Fabric materials',
                'is_active' => true,
                'sort_order' => 8,
            ],
            // Back to School Categories
            [
                'name' => 'Uniform',
                'slug' => 'uniform',
                'description' => 'School uniforms',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Bags',
                'slug' => 'bags',
                'description' => 'School bags and backpacks',
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'name' => 'Stationery',
                'slug' => 'stationery',
                'description' => 'School stationery items',
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'name' => 'Food Container',
                'slug' => 'food_container',
                'description' => 'Lunch boxes and food containers',
                'is_active' => true,
                'sort_order' => 13,
            ],
            [
                'name' => 'Drinkware',
                'slug' => 'drinkware',
                'description' => 'Water bottles and drinkware',
                'is_active' => true,
                'sort_order' => 14,
            ],
            [
                'name' => 'School-Day Essentials',
                'slug' => 'school_day_essentials',
                'description' => 'Essential items for school days',
                'is_active' => true,
                'sort_order' => 15,
            ],
            // Merchandise Categories
            [
                'name' => 'T-Shirts',
                'slug' => 't_shirts',
                'description' => 'School branded t-shirts',
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'name' => 'Hoodies',
                'slug' => 'hoodies',
                'description' => 'School branded hoodies',
                'is_active' => true,
                'sort_order' => 21,
            ],
            [
                'name' => 'Caps',
                'slug' => 'caps',
                'description' => 'School branded caps',
                'is_active' => true,
                'sort_order' => 22,
            ],
            [
                'name' => 'Mugs',
                'slug' => 'mugs',
                'description' => 'School branded mugs',
                'is_active' => true,
                'sort_order' => 23,
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'description' => 'School branded accessories',
                'is_active' => true,
                'sort_order' => 24,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
