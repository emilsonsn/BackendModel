<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Enum\UserGroupEnum;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            [
                "email" => 'user@user.com.br',
            ],
            [
                'name' => 'Usuário',
                'email' => 'user@user.com.br',
                'password' => Hash::make('@123Mudar'),
                'created_at' => date('Y-m-d H:i'),
                'updated_at' => date('Y-m-d H:i')
            ]
        );
    }
}
