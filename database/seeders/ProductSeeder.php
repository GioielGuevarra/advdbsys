<?php
namespace Database\Seeders;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Classic Category
            [
                'product_name' => 'Classic Chocolate Brownie',
                'description' => 'Rich, fudgy chocolate brownie with perfect crackly top',
                'price' => 24.99,
                'product_image' => 'classic-brownie.jpg',
                'categories' => ['Classic']
            ],
            [
                'product_name' => 'Traditional Vanilla Blondie',
                'description' => 'Buttery vanilla brownies with white chocolate chips',
                'price' => 23.99,
                'product_image' => 'vanilla-blondie.jpg',
                'categories' => ['Classic']
            ],
            [
                'product_name' => 'Double Chocolate Chip Cookie',
                'description' => 'Soft-baked chocolate cookies with chocolate chips',
                'price' => 22.99,
                'product_image' => 'chocolate-cookie.jpg',
                'categories' => ['Classic']
            ],
            [
                'product_name' => 'Classic Coffee Cake',
                'description' => 'Cinnamon streusel coffee cake',
                'price' => 25.99,
                'product_image' => 'coffee-cake.jpg',
                'categories' => ['Classic']
            ],
            [
                'product_name' => 'Butter Pound Cake',
                'description' => 'Traditional buttery pound cake',
                'price' => 23.99,
                'product_image' => 'pound-cake.jpg',
                'categories' => ['Classic']
            ],

            // Gourmet Category
            [
                'product_name' => 'Triple Chocolate Fudge Brownie',
                'description' => 'Decadent brownie with three types of chocolate',
                'price' => 35.99,
                'product_image' => 'triple-chocolate.jpg',
                'categories' => ['Gourmet']
            ],
            [
                'product_name' => 'Salted Caramel Brownie',
                'description' => 'Chocolate brownie with salted caramel swirl',
                'price' => 36.99,
                'product_image' => 'caramel-brownie.jpg',
                'categories' => ['Gourmet']
            ],
            [
                'product_name' => 'Nutella-Stuffed Brownie',
                'description' => 'Fudgy brownie with Nutella center',
                'price' => 37.99,
                'product_image' => 'nutella-brownie.jpg',
                'categories' => ['Gourmet']
            ],
            [
                'product_name' => 'Espresso Chocolate Brownie',
                'description' => 'Coffee-infused chocolate brownie',
                'price' => 34.99,
                'product_image' => 'espresso-brownie.jpg',
                'categories' => ['Gourmet']
            ],
            [
                'product_name' => 'Peanut Butter Swirl Brownie',
                'description' => 'Chocolate brownie with peanut butter marble',
                'price' => 35.99,
                'product_image' => 'pb-brownie.jpg',
                'categories' => ['Gourmet']
            ],

            // Bars and Squares
            [
                'product_name' => 'Cheesecake Brownie Bar',
                'description' => 'Marbled cheesecake and brownie bar',
                'price' => 5.99,
                'product_image' => 'cheesecake-bar.jpg',
                'categories' => ['Bars and Squares']
            ],
            [
                'product_name' => 'Mint Chocolate Brownie Square',
                'description' => 'Chocolate brownie with mint layer',
                'price' => 5.99,
                'product_image' => 'mint-square.jpg',
                'categories' => ['Bars and Squares']
            ],
            [
                'product_name' => 'Rocky Road Bar',
                'description' => 'Brownie with marshmallow and nuts',
                'price' => 5.99,
                'product_image' => 'rocky-road.jpg',
                'categories' => ['Bars and Squares']
            ],
            [
                'product_name' => 'Toffee Crunch Bar',
                'description' => 'Brownie base with toffee pieces',
                'price' => 5.99,
                'product_image' => 'toffee-bar.jpg',
                'categories' => ['Bars and Squares']
            ],
            [
                'product_name' => 'S\'mores Brownie Square',
                'description' => 'Graham, chocolate, and marshmallow brownie',
                'price' => 5.99,
                'product_image' => 'smores-square.jpg',
                'categories' => ['Bars and Squares']
            ],

            // Sundaes
            [
                'product_name' => 'Brownie Explosion Sundae',
                'description' => 'Warm brownie with ice cream and fudge',
                'price' => 13.99,
                'product_image' => 'brownie-sundae.jpg',
                'categories' => ['Sundaes']
            ],
            [
                'product_name' => 'Caramel Brownie Blast',
                'description' => 'Caramel brownie with vanilla ice cream',
                'price' => 13.99,
                'product_image' => 'caramel-blast.jpg',
                'categories' => ['Sundaes']
            ],
            [
                'product_name' => 'Cookie Brownie Supreme',
                'description' => 'Cookie and brownie combo sundae',
                'price' => 14.99,
                'product_image' => 'cookie-supreme.jpg',
                'categories' => ['Sundaes']
            ],
            [
                'product_name' => 'Mint Brownie Dream',
                'description' => 'Mint ice cream with chocolate brownie',
                'price' => 13.99,
                'product_image' => 'mint-dream.jpg',
                'categories' => ['Sundaes']
            ],
            [
                'product_name' => 'Peanut Butter Paradise',
                'description' => 'PB brownie with chocolate ice cream',
                'price' => 14.99,
                'product_image' => 'pb-paradise.jpg',
                'categories' => ['Sundaes']
            ],

            // Milkshakes
            [
                'product_name' => 'Brownie Batter Shake',
                'description' => 'Brownie-blended chocolate shake',
                'price' => 9.99,
                'product_image' => 'brownie-shake.jpg',
                'categories' => ['Milkshakes']
            ],
            [
                'product_name' => 'Chocolate Chunk Shake',
                'description' => 'Shake with brownie chunks',
                'price' => 9.99,
                'product_image' => 'chunk-shake.jpg',
                'categories' => ['Milkshakes']
            ],
            [
                'product_name' => 'Caramel Brownie Shake',
                'description' => 'Caramel and brownie pieces blended shake',
                'price' => 10.99,
                'product_image' => 'caramel-shake.jpg',
                'categories' => ['Milkshakes']
            ],
            [
                'product_name' => 'Cookie Brownie Shake',
                'description' => 'Cookies and brownie shake blend',
                'price' => 10.99,
                'product_image' => 'cookie-shake.jpg',
                'categories' => ['Milkshakes']
            ],
            [
                'product_name' => 'Ultimate Fudge Shake',
                'description' => 'Rich fudge brownie milkshake',
                'price' => 11.99,
                'product_image' => 'fudge-shake.jpg',
                'categories' => ['Milkshakes']
            ],

            // Gift Box
            [
                'product_name' => 'Brownie Lovers Box',
                'description' => 'Assortment of our best brownie flavors',
                'price' => 45.99,
                'product_image' => 'lovers-box.jpg',
                'categories' => ['Gift Box']
            ],
            [
                'product_name' => 'Deluxe Brownie Party Pack',
                'description' => 'Perfect for sharing at events',
                'price' => 55.99,
                'product_image' => 'party-box.jpg',
                'categories' => ['Gift Box']
            ],
            [
                'product_name' => 'Gourmet Brownie Selection',
                'description' => 'Premium brownies gift box',
                'price' => 65.99,
                'product_image' => 'gourmet-box.jpg',
                'categories' => ['Gift Box']
            ],
            [
                'product_name' => 'Seasonal Brownie Box',
                'description' => 'Limited edition seasonal flavors',
                'price' => 59.99,
                'product_image' => 'seasonal-box.jpg',
                'categories' => ['Gift Box']
            ],
            [
                'product_name' => 'Corporate Gift Box',
                'description' => 'Perfect for business gifting',
                'price' => 75.99,
                'product_image' => 'corporate-box.jpg',
                'categories' => ['Gift Box']
            ]
        ];

        foreach ($products as $product) {
            $categories = $product['categories'];
            unset($product['categories']);
            
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