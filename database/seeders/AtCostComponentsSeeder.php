<?php

namespace Database\Seeders;

use App\Models\AtCostComponent;
use Illuminate\Database\Seeder;

class AtCostComponentsSeeder extends Seeder
{
    public function run(): void
    {
        $components = [
            [
                'code' => 'RORO',
                'name' => 'Ro-Ro (Roll On Roll Off)',
            ],
            [
                'code' => 'PARKIR_INAP',
                'name' => 'Parkir Inap',
            ],
            [
                'code' => 'TOLL',
                'name' => 'Tol',
            ],
            [
                'code' => 'RAPID_TEST',
                'name' => 'Rapid Test',
            ],
            [
                'code' => 'TAXI',
                'name' => 'Taksi',
            ],
        ];

        foreach ($components as $component) {
            AtCostComponent::create($component);
        }
    }
}
