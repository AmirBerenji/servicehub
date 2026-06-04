<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        DB::table('roles')->insert([
            ['name' => 'guest'],
            ['name' => 'provider'],
            ['name' => 'admin'],
        ]);

        DB::table('categories')->insert([
            ['name' => 'Beauty','description' => 'Beauty','icon'=>'Sparkles',
                'activeClass'=>'border-rose-400 bg-rose-50 text-rose-700',
                'chipClass'=>'bg-rose-100 text-rose-700'],
            ['name' => 'Cleaning','description' => 'Cleaning','icon'=>'SprayCan',
                'activeClass'=>'border-teal-500 bg-teal-50 text-teal-700',
                'chipClass'=>'bg-teal-100 text-teal-700'],
            ['name' => 'Car Service','description' => 'Car Service','icon'=>'Car',
                'activeClass'=>'border-orange-500 bg-orange-50 text-orange-700',
                'chipClass'=>'bg-orange-100 text-orange-700'],
        ]);

        DB::table('services')->insert([
            ['category_id' => '1','name' => 'Hair styling'],
            ['category_id' => '1','name' => 'Nails'],
            ['category_id' => '1','name' => 'Skincare'],
            ['category_id' => '1','name' => 'Makeup'],
            ['category_id' => '1','name' => 'Waxing'],

            ['category_id' => '2','name' => 'Home cleaning'],
            ['category_id' => '2','name' => 'Office cleaning'],
            ['category_id' => '2','name' => 'Deep cleaning'],
            ['category_id' => '2','name' => 'Carpet'],
            ['category_id' => '2','name' => 'Windows'],

            ['category_id' => '3','name' => 'Oil change'],
            ['category_id' => '3','name' => 'Diagnostics'],
            ['category_id' => '3','name' => 'Car wash'],
            ['category_id' => '3','name' => 'Tire service'],
            ['category_id' => '3','name' => 'Towing'],

        ]);
    }
}
