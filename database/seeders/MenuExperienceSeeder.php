<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuExperienceSeeder extends Seeder
{
    public function run(): void
    {
        Product::query()->delete();
        Category::query()->delete();

        $categories = [

            [
                'name' => 'Specialty Coffee',
                'slug' => 'coffee',
                'bg_color' => '#2A4444',
                'text_color' => '#FFFFFF',
                'accent_color' => '#C9A27A',
                'badge' => 'BEST SELLER',
                'products' => [
                    ['Spanish Latte', 120],
                    ['Flat White', 95],
                    ['V60 Colombia', 140],
                    ['Cold Brew', 110],
                    ['Cappuccino', 90],
                    ['Cortado', 85],
                    ['Mocha', 130],
                    ['Caramel Macchiato', 145],
                    ['Iced Latte', 105],
                    ['Americano', 70],
                ]
            ],

            [
                'name' => 'Desserts',
                'slug' => 'desserts',
                'bg_color' => '#5A341F',
                'text_color' => '#FFF7F0',
                'accent_color' => '#F3C892',
                'badge' => 'FRESH',
                'products' => [
                    ['San Sebastian', 180],
                    ['Molten Cake', 160],
                    ['Tiramisu', 175],
                    ['Brownies', 120],
                    ['Chocolate Cookies', 80],
                    ['Cheesecake Lotus', 190],
                    ['Red Velvet', 170],
                    ['Chocolate Tart', 165],
                    ['Pistachio Cake', 220],
                ]
            ],

            [
                'name' => 'Workspace Meals',
                'slug' => 'workspace-meals',
                'bg_color' => '#4C5A3A',
                'text_color' => '#FFFFFF',
                'accent_color' => '#C7D3B0',
                'badge' => 'HEALTHY',
                'products' => [
                    ['Chicken Caesar Wrap', 240],
                    ['Turkey Sandwich', 210],
                    ['Club Sandwich', 260],
                    ['Chicken Panini', 250],
                    ['Protein Bowl', 320],
                    ['Pasta Alfredo', 340],
                    ['Beef Burger', 390],
                    ['Crispy Chicken Burger', 360],
                    ['French Fries', 110],
                    ['Nachos', 180],
                ]
            ],

            [
                'name' => 'Signature Drinks',
                'slug' => 'signature-drinks',
                'bg_color' => '#202020',
                'text_color' => '#FFFFFF',
                'accent_color' => '#C9A27A',
                'badge' => '11\'S SPECIAL',
                'products' => [
                    ['Blue Ocean Mojito', 170],
                    ['Passion Fruit', 165],
                    ['Pink Lemonade', 140],
                    ['Berry Blast', 185],
                    ['Matcha Cloud', 210],
                    ['Iced Hibiscus', 120],
                    ['Mango Freeze', 190],
                    ['Peach Mojito', 175],
                    ['Kiwi Cooler', 160],
                ]
            ],

            [
                'name' => 'Gaming & Night',
                'slug' => 'gaming-night',
                'bg_color' => '#1A1A2E',
                'text_color' => '#FFFFFF',
                'accent_color' => '#8A7CFF',
                'badge' => 'NIGHT MODE',
                'products' => [
                    ['Energy Shot', 95],
                    ['Nitro Cold Brew', 170],
                    ['Dark Mocha', 150],
                    ['Midnight Latte', 165],
                    ['Gaming Combo', 420],
                    ['Cheese Nachos', 190],
                    ['Loaded Fries', 220],
                    ['Chili Hotdog', 240],
                    ['Chicken Bites', 260],
                ]
            ],
        ];

        foreach ($categories as $index => $catData) {

            $products = $catData['products'];

            unset($catData['products']);

            $catData['sort_order'] = $index + 1;

            $category = Category::create($catData);

            foreach ($products as $productIndex => $product) {

                Product::create([
                    'name' => $product[0],

                    'description' =>
                        'Crafted specially for the ELEVEN\'S premium experience with carefully selected ingredients and a smooth luxurious presentation.',

                    'price' => $product[1],

                    'cost' => rand(40, 120),

                    'quantity' => rand(10, 120),

                    'min_quantity' => 5,

                    'is_produced' => rand(0, 1),

                    'category_id' => $category->id,

                    'image' => null,

                    'is_available' => true,

                    'is_featured' => rand(0, 1),

                    'sort_order' => $productIndex + 1,
                ]);
            }
        }
    }
}