<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'Nuevo', 'slug' => 'nuevo'],
            ['name' => 'Oferta', 'slug' => 'oferta'],
            ['name' => 'Popular', 'slug' => 'popular'],
            ['name' => 'Limitado', 'slug' => 'limitado'],
            ['name' => 'Premium', 'slug' => 'premium'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
