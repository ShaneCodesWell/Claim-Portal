<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $company = Company::first();

    $branches = [
        [
            'name'    => 'Head Office',
            'code'    => 'ACC-HQ',
            'email'   => 'headoffice@vanguardassurance.com',
            'phone'   => '030 266 6485',
            'address' => 'P.O. Box 1868, Accra',
        ],
        [
            'name'    => 'Kumasi Branch',
            'code'    => 'KSI-001',
            'email'   => 'kumasi@vanguardassurance.com',
            'phone'   => '',
            'address' => '',
        ],
    ];

    foreach ($branches as $branch) {
        Branch::updateOrCreate(
            ['code' => $branch['code']],
            array_merge($branch, ['company_id' => $company->id])
        );
    }
}
}
