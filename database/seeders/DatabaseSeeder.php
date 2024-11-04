<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@test.com',
        // ]);
        User::query()->updateOrCreate([
            'email' => 'admin@admin.com'
        ], [
            'name'        => 'Dev Super Admin',
            'role'        => 'super_admin',
            'password'    => Hash::make('admin1234')
        ]);
        User::query()->updateOrCreate([
            'email' => 'client@client.com'
        ], [
            'name'        => 'Dev Client',
            'role'        => 'admin',
            'password'    => Hash::make('client1234')
        ]);

        // for ($i = 1; $i <= 100; $i++) {
        //     User::query()->updateOrCreate([
        //         'email' => 'admin' . $i . '@example.com'
        //     ], [
        //         'name'     => 'Admin User ' . $i,
        //         'role'     => 'admin',
        //         'password' => Hash::make('password1234')
        //     ]);
        // }
    }
}
