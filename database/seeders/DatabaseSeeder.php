<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $business = \App\Models\Business::create([
            'name' => 'Default Business',
            'document' => '123456789',
        ]);

         \App\Models\User::create([
             'name' => 'Default User',
             'business_id' => $business->id,
             'email' => 'gabriel@example.com',
             'password' => Hash::make('password'),
         ]);
    }
}
