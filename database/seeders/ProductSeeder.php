<?php
namespace Database\Seeders;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create products directory if it doesn't exist
        Storage::disk('public')->makeDirectory('products');

        $products = [
            // Classic Category
            [
                'product_name' => 'Classic Chocolate Brownie',
                'description' => 'Rich, fudgy chocolate brownie with perfect crackly top',
                'price' => 124.99,
                'product_image' => 'classic-brownie.jpg',
                'categories' => ['Classic']
            ],
            [
                'product_name' => 'Traditional Vanilla Blondie',
                'description' => 'Buttery vanilla brownies with white chocolate chips',
                'price' => 123.99,
                'product_image' => 'vanilla-blondie.jpg',
                'categories' => ['Classic']
            ],
            [
                'product_name' => 'Double Chocolate Chip Cookie',
                'description' => 'Soft-baked chocolate cookies with chocolate chips',
                'price' => 122.99,
                'product_image' => 'chocolate-cookie.jpg',
                'categories' => ['Classic']
            ],
            [
                'product_name' => 'Classic Coffee Cake',
                'description' => 'Cinnamon streusel coffee cake',
                'price' => 125.99,
                'product_image' => 'coffee-cake.jpg',
                'categories' => ['Classic']
            ],
            [
                'product_name' => 'Butter Pound Cake',
                'description' => 'Traditional buttery pound cake',
                'price' => 123.99,
                'product_image' => 'pound-cake.jpg',
                'categories' => ['Classic']
            ],

            // Gourmet Category
            [
                'product_name' => 'Triple Chocolate Fudge Brownie',
                'description' => 'Decadent brownie with three types of chocolate',
                'price' => 135.99,
                'product_image' => 'triple-chocolate.jpg',
                'categories' => ['Gourmet']
            ],
            [
                'product_name' => 'Salted Caramel Brownie',
                'description' => 'Chocolate brownie with salted caramel swirl',
                'price' => 136.99,
                'product_image' => 'caramel-brownie.jpg',
                'categories' => ['Gourmet']
            ],
            [
                'product_name' => 'Nutella-Stuffed Brownie',
                'description' => 'Fudgy brownie with Nutella center',
                'price' => 137.99,
                'product_image' => 'nutella-brownie.jpg',
                'categories' => ['Gourmet']
            ],
            [
                'product_name' => 'Espresso Chocolate Brownie',
                'description' => 'Coffee-infused chocolate brownie',
                'price' => 134.99,
                'product_image' => 'espresso-brownie.jpg',
                'categories' => ['Gourmet']
            ],
            [
                'product_name' => 'Peanut Butter Swirl Brownie',
                'description' => 'Chocolate brownie with peanut butter marble',
                'price' => 135.99,
                'product_image' => 'pb-brownie.jpg',
                'categories' => ['Gourmet']
            ],

            // Bars and Squares
            [
                'product_name' => 'Cheesecake Brownie Bar',
                'description' => 'Marbled cheesecake and brownie bar',
                'price' => 89.99,
                'product_image' => 'cheesecake-bar.jpg',
                'categories' => ['Bars and Squares']
            ],
            [
                'product_name' => 'Mint Chocolate Brownie Square',
                'description' => 'Chocolate brownie with mint layer',
                'price' => 89.99,
                'product_image' => 'mint-square.jpg',
                'categories' => ['Bars and Squares']
            ],
            [
                'product_name' => 'Rocky Road Bar',
                'description' => 'Brownie with marshmallow and nuts',
                'price' => 89.99,
                'product_image' => 'rocky-road.jpg',
                'categories' => ['Bars and Squares']
            ],
            [
                'product_name' => 'Toffee Crunch Bar',
                'description' => 'Brownie base with toffee pieces',
                'price' => 89.99,
                'product_image' => 'toffee-bar.jpg',
                'categories' => ['Bars and Squares']
            ],
            [
                'product_name' => 'S\'mores Brownie Square',
                'description' => 'Graham, chocolate, and marshmallow brownie',
                'price' => 89.99,
                'product_image' => 'smores-square.jpg',
                'categories' => ['Bars and Squares']
            ],

            // Sundaes
            [
                'product_name' => 'Brownie Explosion Sundae',
                'description' => 'Warm brownie with ice cream and fudge',
                'price' => 149.99,
                'product_image' => 'brownie-sundae.jpg',
                'categories' => ['Sundaes']
            ],
            [
                'product_name' => 'Caramel Brownie Blast',
                'description' => 'Caramel brownie with vanilla ice cream',
                'price' => 149.99,
                'product_image' => 'caramel-blast.jpg',
                'categories' => ['Sundaes']
            ],
            [
                'product_name' => 'Cookie Brownie Supreme',
                'description' => 'Cookie and brownie combo sundae',
                'price' => 149.99,
                'product_image' => 'cookie-supreme.jpg',
                'categories' => ['Sundaes']
            ],
            [
                'product_name' => 'Mint Brownie Dream',
                'description' => 'Mint ice cream with chocolate brownie',
                'price' => 149.99,
                'product_image' => 'mint-dream.jpg',
                'categories' => ['Sundaes']
            ],
            [
                'product_name' => 'Peanut Butter Paradise',
                'description' => 'PB brownie with chocolate ice cream',
                'price' => 149.99,
                'product_image' => 'pb-paradise.jpg',
                'categories' => ['Sundaes']
            ],

            // Milkshakes
            [
                'product_name' => 'Brownie Batter Shake',
                'description' => 'Brownie-blended chocolate shake',
                'price' => 99.99,
                'product_image' => 'brownie-shake.jpg',
                'categories' => ['Milkshakes']
            ],
            [
                'product_name' => 'Chocolate Chunk Shake',
                'description' => 'Shake with brownie chunks',
                'price' => 99.99,
                'product_image' => 'chunk-shake.jpg',
                'categories' => ['Milkshakes']
            ],
            [
                'product_name' => 'Caramel Brownie Shake',
                'description' => 'Caramel and brownie pieces blended shake',
                'price' => 99.99,
                'product_image' => 'caramel-shake.jpg',
                'categories' => ['Milkshakes']
            ],
            [
                'product_name' => 'Cookie Brownie Shake',
                'description' => 'Cookies and brownie shake blend',
                'price' => 99.99,
                'product_image' => 'cookie-shake.jpg',
                'categories' => ['Milkshakes']
            ],
            [
                'product_name' => 'Ultimate Fudge Shake',
                'description' => 'Rich fudge brownie milkshake',
                'price' => 99.99,
                'product_image' => 'fudge-shake.jpg',
                'categories' => ['Milkshakes']
            ],

            // Gift Box
            [
                'product_name' => 'Brownie Lovers Box',
                'description' => 'Assortment of our best brownie flavors',
                'price' => 145.99,
                'product_image' => 'lovers-box.jpg',
                'categories' => ['Gift Box']
            ],
            [
                'product_name' => 'Deluxe Brownie Party Pack',
                'description' => 'Perfect for sharing at events',
                'price' => 155.99,
                'product_image' => 'party-box.jpg',
                'categories' => ['Gift Box']
            ],
            [
                'product_name' => 'Gourmet Brownie Selection',
                'description' => 'Premium brownies gift box',
                'price' => 165.99,
                'product_image' => 'gourmet-box.jpg',
                'categories' => ['Gift Box']
            ],
            [
                'product_name' => 'Seasonal Brownie Box',
                'description' => 'Limited edition seasonal flavors',
                'price' => 159.99,
                'product_image' => 'seasonal-box.jpg',
                'categories' => ['Gift Box']
            ],
            [
                'product_name' => 'Corporate Gift Box',
                'description' => 'Perfect for business gifting',
                'price' => 175.99,
                'product_image' => 'corporate-box.jpg',
                'categories' => ['Gift Box']
            ]
        ];

        foreach ($products as $product) {
            $categories = $product['categories'];
            unset($product['categories']);
            
            // Update the image path to not include 'products/' since it's already in the full path
            $product['product_image'] = basename($product['product_image']);
            
            // Verify image exists or use default
            if (!Storage::disk('public')->exists('products/' . $product['product_image'])) {
                $product['product_image'] = 'default.jpg';
            }
            
            $newProduct = Product::create($product);
            
            foreach ($categories as $categoryName) {
                $category = Category::where('category_name', $categoryName)->first();
                if ($category) {
                    $newProduct->categories()->attach($category->category_id);
                }
            }
        }
    }
}