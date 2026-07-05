<?php

namespace Database\Seeders;

use App\Models\FormTemplate;
use Illuminate\Database\Seeder;

class FormTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $this->publish('motor', 1, 'Motor Accident Report Form', base_path('schemas/motor_v1.json'));
        $this->publish('fire', 1, 'Fire Claim Form', base_path('schemas/fire_v1.json'));
    }

    protected function publish(string $productType, int $version, string $name, string $schemaPath): void
    {
        FormTemplate::updateOrCreate(
            ['product_type' => $productType, 'version' => $version],
            [
                'name' => $name,
                'status' => 'published',
                'schema' => json_decode(file_get_contents($schemaPath), true),
                'published_at' => now(),
            ]
        );
    }
}