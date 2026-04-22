<?php
namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::updateOrCreate(
        ['id' => 1],
        [
            'name'             => 'Vanguard Assurance Company Ltd',
            'tagline'          => 'We always stand by you',
            'email'            => 'vacmmails@vanguardassurance.com',
            'claims_email'     => 'claimsdepartment@vanguardassurance.com',
            'phone_primary'    => '030 266 6485',
            'phone_secondary'  => '030 266 6486',
            'phone_tertiary'   => '030 266 6487',
            'postal_address'   => 'P.O. Box 1868, Accra',
            'physical_address' => '',
            'website'          => 'https://vanguardassurance.com',
        ]
    );
    }
}
