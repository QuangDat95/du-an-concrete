<?php
namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Webpatser\Uuid\Uuid;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Toan',
            'email' => 'toan@odinbi.com',
            'email_verified_at' => now(),
            'password' => Hash::make('abcd1234'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
