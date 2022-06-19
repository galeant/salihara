<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrcreate([
            'name' => 'admin',
            'email' => 'admin@mail.com'
        ], [
            'password' => Hash::make('admin'),
            'role' => 'admin'
        ]);

        User::firstOrcreate([
            'name' => 'customer',
            'email' => 'customer@mail.com'
        ], [
            'password' => Hash::make('customer'),
            'role' => 'customer'
        ]);
    }
}
