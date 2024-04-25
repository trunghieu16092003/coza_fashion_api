<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
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
        $newUser = new User();
        $newUser->name = 'Nguyễn Văn A';
        $newUser->email = 'hello@gmail.com';
        $newUser->password = Hash::make('12345678');

        $newUser->save();
    }
}
