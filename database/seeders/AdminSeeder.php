<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@vanguardassurance.com'],
            [
                'name'          => 'Super Admin',
                'password'      => bcrypt('password'),
                'is_admin'      => true,
                'role'          => 'admin',
                'branch_id'     => 1,
                'department_id' => 1,
                'phone'         => null,
                'is_active'     => true,
            ]
        );
    }
}
