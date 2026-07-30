<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company    = Company::first();
        $headOffice = Branch::where('code', '1000')->first();

        if (! $company || ! $headOffice) {
            throw new \RuntimeException('DepartmentSeeder: missing required Company or Head Office Branch — check CompanySeeder/BranchSeeder ran first.');
        }

        $departments = [
            [
                'name'        => 'Claims',
                'code'        => 'CLAIMS',
                'description' => 'Handles all insurance claim processing',
            ],
            [
                'name'        => 'Underwriting',
                'code'        => 'UNDERWRITING',
                'description' => 'Policy underwriting and risk assessment',
            ],
            [
                'name'        => 'Finance',
                'code'        => 'FINANCE',
                'description' => 'Financial operations and reporting',
            ],
            [
                'name'        => 'Customer Service',
                'code'        => 'CUSTOMER-SVC',
                'description' => 'Customer support and relations',
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['code' => $department['code']],
                array_merge($department, [
                    'company_id' => $company->id,
                    'branch_id'  => $headOffice->id,
                ])
            );
        }
    }
}
