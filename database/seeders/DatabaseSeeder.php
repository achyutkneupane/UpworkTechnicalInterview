<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $user1 = User::create([
            'name' => "Name 1",
            'email' => 'email1@example.com',
            'password' => Hash::make('password1')
        ]);
        $user2 = User::create([
            'name' => "Name 2",
            'email' => 'email2@example.com',
            'password' => Hash::make('password2')
        ]);
        $user3 = User::create([
            'name' => "Name 3",
            'email' => 'email3@example.com',
            'password' => Hash::make('password3')
        ]);

    }
}
