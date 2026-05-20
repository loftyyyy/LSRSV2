<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'Name'=>'admin',
            'Email'=>'admin@admin.com',
            'is_admin'=>true,
            'Password'=>Hash::make('admin'),
        ]);

        User::create([
            'Name'=>'clerk',
            'Email'=>'clerk@clerk.com',
            'is_admin'=>false,
            'Password'=>Hash::make('clerk'),
        ]);

        User::create([
            'Name'=>'rho',
            'Email'=>'r.jornadal.544960@umindanao.edu.ph',
            'is_admin'=>true,
            'Password'=>Hash::make('rho'),
        ]);
    }
}
