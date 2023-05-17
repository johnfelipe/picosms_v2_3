<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [[
            'name'           => 'Admin',
            'email'          => 'admin@demo.com',
            'password'       => bcrypt('123456'),
            'remember_token' => null,
            'created_at'     => now(),
            'updated_at'     => now(),
            'deleted_at'     => null,
        ]];

        \App\Models\User::insert($users);
    }
}
