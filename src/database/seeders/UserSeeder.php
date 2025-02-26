<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 一般ユーザーのダミーデータ
        User::create([
            'name' => '一般ユーザー',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'user', // 役割を user に設定
        ]);

        // 複数の一般ユーザーを作成
        User::factory()->count(10)->create();
    }
}
