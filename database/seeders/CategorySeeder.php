<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Main Categories
        $electronics = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic devices and accessories',
            'is_active' => true,
            'order' => 1,
        ]);

        $fashion = Category::create([
            'name' => 'Fashion',
            'slug' => 'fashion',
            'description' => 'Clothing, shoes, and accessories',
            'is_active' => true,
            'order' => 2,
        ]);

        $home = Category::create([
            'name' => 'Home & Garden',
            'slug' => 'home-garden',
            'description' => 'Home improvement and garden supplies',
            'is_active' => true,
            'order' => 3,
        ]);

        $sports = Category::create([
            'name' => 'Sports & Outdoors',
            'slug' => 'sports-outdoors',
            'description' => 'Sports equipment and outdoor gear',
            'is_active' => true,
            'order' => 4,
        ]);

        $books = Category::create([
            'name' => 'Books',
            'slug' => 'books',
            'description' => 'Books and literature',
            'is_active' => true,
            'order' => 5,
        ]);

        // Electronics Subcategories
        Category::create([
            'name' => 'Smartphones',
            'slug' => 'smartphones',
            'description' => 'Mobile phones and accessories',
            'parent_id' => $electronics->id,
            'is_active' => true,
            'order' => 1,
        ]);

        Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
            'description' => 'Laptops and notebooks',
            'parent_id' => $electronics->id,
            'is_active' => true,
            'order' => 2,
        ]);

        Category::create([
            'name' => 'Cameras',
            'slug' => 'cameras',
            'description' => 'Digital cameras and photography equipment',
            'parent_id' => $electronics->id,
            'is_active' => true,
            'order' => 3,
        ]);

        Category::create([
            'name' => 'Audio',
            'slug' => 'audio',
            'description' => 'Headphones, speakers, and audio equipment',
            'parent_id' => $electronics->id,
            'is_active' => true,
            'order' => 4,
        ]);

        // Fashion Subcategories
        Category::create([
            'name' => 'Men\'s Clothing',
            'slug' => 'mens-clothing',
            'description' => 'Clothing for men',
            'parent_id' => $fashion->id,
            'is_active' => true,
            'order' => 1,
        ]);

        Category::create([
            'name' => 'Women\'s Clothing',
            'slug' => 'womens-clothing',
            'description' => 'Clothing for women',
            'parent_id' => $fashion->id,
            'is_active' => true,
            'order' => 2,
        ]);

        Category::create([
            'name' => 'Shoes',
            'slug' => 'shoes',
            'description' => 'Footwear for all',
            'parent_id' => $fashion->id,
            'is_active' => true,
            'order' => 3,
        ]);

        Category::create([
            'name' => 'Accessories',
            'slug' => 'fashion-accessories',
            'description' => 'Fashion accessories and jewelry',
            'parent_id' => $fashion->id,
            'is_active' => true,
            'order' => 4,
        ]);

        // Home & Garden Subcategories
        Category::create([
            'name' => 'Furniture',
            'slug' => 'furniture',
            'description' => 'Home and office furniture',
            'parent_id' => $home->id,
            'is_active' => true,
            'order' => 1,
        ]);

        Category::create([
            'name' => 'Kitchen',
            'slug' => 'kitchen',
            'description' => 'Kitchen appliances and utensils',
            'parent_id' => $home->id,
            'is_active' => true,
            'order' => 2,
        ]);

        Category::create([
            'name' => 'Garden Tools',
            'slug' => 'garden-tools',
            'description' => 'Gardening tools and equipment',
            'parent_id' => $home->id,
            'is_active' => true,
            'order' => 3,
        ]);

        // Sports Subcategories
        Category::create([
            'name' => 'Fitness',
            'slug' => 'fitness',
            'description' => 'Fitness equipment and gear',
            'parent_id' => $sports->id,
            'is_active' => true,
            'order' => 1,
        ]);

        Category::create([
            'name' => 'Camping',
            'slug' => 'camping',
            'description' => 'Camping and outdoor equipment',
            'parent_id' => $sports->id,
            'is_active' => true,
            'order' => 2,
        ]);

        Category::create([
            'name' => 'Team Sports',
            'slug' => 'team-sports',
            'description' => 'Equipment for team sports',
            'parent_id' => $sports->id,
            'is_active' => true,
            'order' => 3,
        ]);

        // Books Subcategories
        Category::create([
            'name' => 'Fiction',
            'slug' => 'fiction',
            'description' => 'Fiction books and novels',
            'parent_id' => $books->id,
            'is_active' => true,
            'order' => 1,
        ]);

        Category::create([
            'name' => 'Non-Fiction',
            'slug' => 'non-fiction',
            'description' => 'Non-fiction and educational books',
            'parent_id' => $books->id,
            'is_active' => true,
            'order' => 2,
        ]);

        $this->command->info('Categories seeded successfully!');
    }
}