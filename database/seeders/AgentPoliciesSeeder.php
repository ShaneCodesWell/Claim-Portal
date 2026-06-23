<?php
namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Policy;
use Illuminate\Database\Seeder;


class AgentPoliciesSeeder extends Seeder
{
    public function run(): void
    {
        // Grab your local test agent
        // Swap the phone number for whatever you used when registering locally
        $agent = Agent::where('phone', '+233241277284')->firstOrFail();

        $policies = [

            // 1. Single-risk motor policy
            [
                'customer_id'         => 999001,
                'agent_id'            => $agent->id,
                'source'              => 'glims',
                'external_policy_id'  => '30999999',
                'policy_number'       => 'P-TEST-GLIMS-SINGLE-001',
                'product_id'          => '203',
                'product_name'        => 'MOTOR THIRD PARTY ONLY',
                'business_class_id'   => null,
                'business_class_name' => 'MOTOR',
                'start_date'          => '2025-03-01',
                'end_date'            => '2026-03-01',
                'effective_date'      => '2025-03-01',
                'renewal_date'        => '2026-03-01',
                'last_synced_at'      => now(),
                'raw_payload'         => [
                    'POLICY_NUMBER'        => 'P-TEST-GLIMS-SINGLE-001',
                    'POLICY_ID'            => 30999999,
                    'POLICY_CREATED_AT'    => '2025-03-01T09:00:00',
                    'POLICY_START_DATE'    => '2025-03-01',
                    'POLICY_EXPIRY_DATE'   => '2026-03-01',
                    'POLICY_ISSUE_DATE'    => '2025-03-01',
                    'POLICY_LOB_NAME'      => 'MOTOR',
                    'POLICY_PRODUCT_NAME'  => 'MOTOR THIRD PARTY ONLY',
                    'POLICY_PRODUCT_CODE'  => '203',
                    'POLICY_BRANCH_NAME'   => 'EAST LEGON',
                    'POLICY_AGENT_CODE'    => $agent->agent_code ?? 30004,
                    'POLICY_AGENT_NAME'    => $agent->name ?? 'TEST AGENT',
                    'POLICY_CURRENCY'      => 'GHC',
                    'POLICY_TOTAL_PREMIUM' => 482,
                    'POLICY_TOTAL_SI'      => 0,
                    'CUSTOMER_CODE'        => '55797',
                    'CUSTOMER_FIRST_NAME'  => 'DERRICK',
                    'CUSTOMER_OTHER_NAMES' => null,
                    'CUSTOMER_FAMILY_NAME' => 'BEDU',
                    'is_fleet'             => false,
                    'source'               => 'glims',
                    'status_label'         => 'active',
                    'risks'                => [
                        [
                            'risk_ref_no'            => 'GR 8080 U',
                            'vehicle_make'           => 'TOYOTA',
                            'vehicle_model'          => 'CAMRY',
                            'vehicle_yr_manufacture' => 2019,
                            'vehicle_chassis_no'     => 'JTDBF30K200123456',
                            'vehicle_colour'         => 'SILVER',
                            'vehicle_body_type'      => 'SALOON',
                            'seats'                  => 5,
                            'cubic_capacity'         => 2500,
                            'usage'                  => 'PRIVATE CARS (INDIVIDUAL) - X1',
                            'sum_insured'            => 0,
                            'total_premium'          => 482,
                            'covers'                 => [],
                        ],
                    ],
                ],
            ],

            // 2. Fleet motor policy (3 vehicles)
            [
                'customer_id'         => 999002,
                'agent_id'            => $agent->id,
                'source'              => 'glims',
                'external_policy_id'  => '30000000',
                'policy_number'       => 'P-TEST-GLIMS-FLEET-001',
                'product_id'          => '201',
                'product_name'        => 'MOTOR COMPREHENSIVE',
                'business_class_id'   => null,
                'business_class_name' => 'MOTOR',
                'start_date'          => '2025-06-01',
                'end_date'            => '2026-06-01',
                'effective_date'      => '2025-06-01',
                'renewal_date'        => '2026-06-01',
                'last_synced_at'      => now(),
                'raw_payload'         => [
                    'POLICY_NUMBER'        => 'P-TEST-GLIMS-FLEET-001',
                    'POLICY_ID'            => 30000000,
                    'POLICY_CREATED_AT'    => '2025-06-01T10:30:00',
                    'POLICY_START_DATE'    => '2025-06-01',
                    'POLICY_EXPIRY_DATE'   => '2026-06-01',
                    'POLICY_ISSUE_DATE'    => '2025-06-01',
                    'POLICY_LOB_NAME'      => 'MOTOR',
                    'POLICY_PRODUCT_NAME'  => 'MOTOR COMPREHENSIVE',
                    'POLICY_PRODUCT_CODE'  => '201',
                    'POLICY_BRANCH_NAME'   => 'ACHIMOTA',
                    'POLICY_AGENT_CODE'    => $agent->agent_code ?? 30004,
                    'POLICY_AGENT_NAME'    => $agent->name ?? 'TEST AGENT',
                    'POLICY_CURRENCY'      => 'GHC',
                    'POLICY_TOTAL_PREMIUM' => 4200,
                    'POLICY_TOTAL_SI'      => 120000,
                    'CUSTOMER_CODE'        => '11807',
                    'CUSTOMER_FIRST_NAME'  => null,
                    'CUSTOMER_OTHER_NAMES' => null,
                    'CUSTOMER_FAMILY_NAME' => 'SUN TRADE BEADS',
                    'is_fleet'             => true,
                    'source'               => 'glims',
                    'status_label'         => 'active',
                    'risks'                => [
                        [
                            'risk_ref_no'            => 'GR 1234 A',
                            'vehicle_make'           => 'TOYOTA',
                            'vehicle_model'          => 'HILUX',
                            'vehicle_yr_manufacture' => 2020,
                            'vehicle_chassis_no'     => 'AHTFZ29G506001111',
                            'vehicle_colour'         => 'WHITE',
                            'vehicle_body_type'      => 'PICKUP',
                            'seats'                  => 5,
                            'cubic_capacity'         => 2700,
                            'usage'                  => 'COMMERCIAL VEHICLES - X4',
                            'sum_insured'            => 40000,
                            'total_premium'          => 1400,
                            'covers'                 => [],
                        ],
                        [
                            'risk_ref_no'            => 'GR 5678 B',
                            'vehicle_make'           => 'NISSAN',
                            'vehicle_model'          => 'NAVARA',
                            'vehicle_yr_manufacture' => 2018,
                            'vehicle_chassis_no'     => 'VSKJUD21U00022222',
                            'vehicle_colour'         => 'BLUE',
                            'vehicle_body_type'      => 'PICKUP',
                            'seats'                  => 5,
                            'cubic_capacity'         => 2500,
                            'usage'                  => 'COMMERCIAL VEHICLES - X4',
                            'sum_insured'            => 35000,
                            'total_premium'          => 1350,
                            'covers'                 => [],
                        ],
                        [
                            'risk_ref_no'            => 'GR 9012 C',
                            'vehicle_make'           => 'FORD',
                            'vehicle_model'          => 'RANGER',
                            'vehicle_yr_manufacture' => 2021,
                            'vehicle_chassis_no'     => 'WF0FXXTTGFKJ33333',
                            'vehicle_colour'         => 'GREY',
                            'vehicle_body_type'      => 'PICKUP',
                            'seats'                  => 5,
                            'cubic_capacity'         => 3200,
                            'usage'                  => 'COMMERCIAL VEHICLES - X4',
                            'sum_insured'            => 45000,
                            'total_premium'          => 1450,
                            'covers'                 => [],
                        ],
                    ],
                ],
            ],

        ];

        foreach ($policies as $data) {
            Policy::create($data);
        }

        $this->command->info('Seeded 2 test GLIMS policies (1 single-risk, 1 fleet) for agent: ' . $agent->id);
    }
}
