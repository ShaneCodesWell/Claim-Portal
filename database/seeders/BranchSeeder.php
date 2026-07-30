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
                'code'    => '1000',
                'email'   => 'vacmails@vanguardassurance.com',
                'phone'   => '0505811511',
                'address' => '',
            ],
            [
                'name'    => 'Broker',
                'code'    => '1001',
                'email'   => 'broker@vanguardassurance.com',
                'phone'   => '0243968126',
                'address' => '',
            ],
            [
                'name'    => 'Corporate',
                'code'    => '1002',
                'email'   => 'corporate@vanguardassurance.com',
                'phone'   => '0549111775',
                'address' => '',
            ],
            [
                'name'    => 'Accra Main',
                'code'    => '1003',
                'email'   => 'accramain@vanguardassurance.com',
                'phone'   => '0505811511',
                'address' => '',
            ],
            [
                'name'    => 'Reinsurance',
                'code'    => '1004',
                'email'   => 'reinsurance@vanguardassurance.com',
                'phone'   => '0243968126',
                'address' => '',
            ],
            [
                'name'    => 'Horizon',
                'code'    => '1005',
                'email'   => 'horizon@gmail.com',
                'phone'   => '0549111775',
                'address' => '',
            ],
            [
                'name'    => 'Dansoman',
                'code'    => '1012',
                'email'   => 'dansoman@vanguardassurance.com',
                'phone'   => '0244334407',
                'address' => '',
            ],
            [
                'name'    => 'Achimota',
                'code'    => '1013',
                'email'   => 'achimota@vanguardassurance.com',
                'phone'   => '0244334407',
                'address' => '',
            ],
            [
                'name'    => 'Adenta',
                'code'    => '1014',
                'email'   => 'adenta@vanguardassurance.com',
                'phone'   => '0244334407',
                'address' => '',
            ],
            [
                'name'    => 'East Legon',
                'code'    => '1015',
                'email'   => 'eastlegon@vanguardassurance.com',
                'phone'   => '0244334407',
                'address' => '',
            ],
            [
                'name'    => 'Tema',
                'code'    => '1021',
                'email'   => 'tema@vanguardassurance.com',
                'phone'   => '0244334407',
                'address' => '',
            ],
            [
                'name'    => 'Business Development Unit',
                'code'    => '1022',
                'email'   => 'bdu@vanguardassurance.com',
                'phone'   => '0208270680',
                'address' => '',
            ],
            [
                'name'    => 'Spintex',
                'code'    => '1023',
                'email'   => 'spintex@vanguardassurance.com',
                'phone'   => '0244334407',
                'address' => '',
            ],
            [
                'name'    => 'West Hills',
                'code'    => '1031',
                'email'   => 'westhills@vanguardassurance.com',
                'phone'   => '0244334407',
                'address' => '',
            ],
            [
                'name'    => 'Kumasi',
                'code'    => '1101',
                'email'   => 'kumasi@vanguardassurance.com',
                'phone'   => '0244334407',
                'address' => '',
            ],
            [
                'name'    => 'Obuasi',
                'code'    => '1102',
                'email'   => 'info@vanguardassurance.com',
                'phone'   => '',
                'address' => '',
            ],
            [
                'name'    => 'Cape Coast',
                'code'    => '1201',
                'email'   => 'capecoast@vanguardassurance.com',
                'phone'   => '0244334407',
                'address' => '',
            ],
            [
                'name'    => 'Swedru',
                'code'    => '1202',
                'email'   => 'info@vanguardassurance.com',
                'phone'   => '',
                'address' => '',
            ],
            [
                'name'    => 'Takoradi',
                'code'    => '1301',
                'email'   => 'takoradi@vanguardassurance.com',
                'phone'   => '0244334407',
                'address' => '',
            ],
            [
                'name'    => 'Tarkwa',
                'code'    => '1302',
                'email'   => 'tarkwa@vanguardassurance.com',
                'phone'   => '0000000002',
                'address' => '',
            ],
            [
                'name'    => 'Sefwi Wiawso',
                'code'    => '1311',
                'email'   => 'info@vanguardassurance.com',
                'phone'   => '',
                'address' => '',
            ],
            [
                'name'    => 'Sunyani',
                'code'    => '1401',
                'email'   => 'info@vanguardassurance.com',
                'phone'   => '0244334407',
                'address' => '',
            ],
            [
                'name'    => 'Techiman',
                'code'    => '1411',
                'email'   => 'info@vanguardassurance.com',
                'phone'   => '',
                'address' => '',
            ],
            [
                'name'    => 'Gawsu',
                'code'    => '1421',
                'email'   => 'info@vanguardassurance.com',
                'phone'   => '',
                'address' => '',
            ],
            [
                'name'    => 'Koforidua',
                'code'    => '1501',
                'email'   => 'koforidua@vanguardassurance.com',
                'phone'   => '0244334407',
                'address' => '',
            ],
            [
                'name'    => 'Ho',
                'code'    => '1601',
                'email'   => 'info@vanguardassurance.com',
                'phone'   => '',
                'address' => '',
            ],
            [
                'name'    => 'Aflao',
                'code'    => '1602',
                'email'   => 'aflao@vanguardassurance.com',
                'phone'   => '0505811511',
                'address' => '',
            ],
            [
                'name'    => 'Dangbai',
                'code'    => '1611',
                'email'   => 'info@vanguardassurance.com',
                'phone'   => '',
                'address' => '',
            ],
            [
                'name'    => 'Tamale',
                'code'    => '1701',
                'email'   => 'tamale@vanguardassurance.com',
                'phone'   => '0244334407',
                'address' => '',
            ],
            [
                'name'    => 'Nalerugu',
                'code'    => '1711',
                'email'   => 'info@vanguardassurance.com',
                'phone'   => '',
                'address' => '',
            ],
            [
                'name'    => 'Bolgatanga',
                'code'    => '1801',
                'email'   => 'info@vanguardassurance.com',
                'phone'   => '',
                'address' => '',
            ],
            [
                'name'    => 'Wa',
                'code'    => '1901',
                'email'   => 'info@vanguardassurance.com',
                'phone'   => '0908765433',
                'address' => '',
            ],
            [
                'name'    => 'Finance Branch - Atsu',
                'code'    => '1902',
                'email'   => 'financeatsu@vanguardassurance.com',
                'phone'   => '0505811511',
                'address' => '',
            ],
            [
                'name'    => 'Finance Branch - Andy',
                'code'    => '1903',
                'email'   => 'financeandy@vanguardassurance.com',
                'phone'   => '0505811511',
                'address' => '',
            ],
            [
                'name'    => 'Finance Branch - Mark',
                'code'    => '1904',
                'email'   => 'accramain@vanguardassurance.com',
                'phone'   => '0505811511',
                'address' => '',
            ],
            [
                'name'    => 'Finance Branch - Leticia',
                'code'    => '1905',
                'email'   => 'financeleticia@vanguardassurance.com',
                'phone'   => '0505811511',
                'address' => '',
            ],
            [
                'name'    => 'Dummy Unit',
                'code'    => '1906',
                'email'   => 'accramain@vanguardassurance.com',
                'phone'   => '0505811511',
                'address' => '',
            ],
            [
                'name'    => 'Unresolved',
                'code'    => 'UNRESOLVED',
                'email'   => '',
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
