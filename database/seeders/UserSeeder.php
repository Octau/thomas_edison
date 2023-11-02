<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = new User([
            'name'      => 'Admin Thomas',
            'email'     => 'admin@thomas.com',
            'password'  => Hash::make('secret'),
        ]);
        $user->saveOrFail();
    }
}
