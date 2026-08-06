<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AgentTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'name',
            'email',
            'phone',
            'gender',
            'date_of_birth',
            'league',
            'glims_agent_code',
            'genova_agent_code',
            'branch',
            'user_category',
            'sub_user_category',
        ];
    }

    public function array(): array
    {
        return [
            ['Jane Doe', 'jane@example.com', '0244000000', 'female', '1990-01-15', 'champions', '30004', 'AG-0507', 'Accra Main', 'Agent', ''],
        ];
    }
}
