<?php

namespace Database\Seeders;

use App\Models\ComponentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ComponentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $hasStatusOnly = Schema::hasTable('component_types') && Schema::hasColumn('component_types', 'status_only');

        $types = [
            [
                'name' => 'Disk',
                'slug' => 'disk',
                'sort_order' => 10,
                'status_only' => true,
                'attributes' => [
                    ['name' => 'Volume', 'slug' => 'volume'],
                    ['name' => 'Capacity', 'slug' => 'capacity'],
                ],
            ],
            [
                'name' => 'RAM',
                'slug' => 'ram',
                'sort_order' => 20,
                'status_only' => false,
                'attributes' => [
                    ['name' => 'Size', 'slug' => 'size'],
                    ['name' => 'Speed', 'slug' => 'speed'],
                ],
            ],
            [
                'name' => 'CPU',
                'slug' => 'cpu',
                'sort_order' => 30,
                'status_only' => false,
                'attributes' => [
                    ['name' => 'Model', 'slug' => 'model'],
                    ['name' => 'Cores', 'slug' => 'cores'],
                ],
            ],
            [
                'name' => 'PSU',
                'slug' => 'psu',
                'sort_order' => 40,
                'status_only' => true,
                'attributes' => [
                    ['name' => 'Power (W)', 'slug' => 'power-w'],
                ],
            ],
        ];

        foreach ($types as $data) {
            $payload = [
                'name' => $data['name'],
                'sort_order' => $data['sort_order'],
                'attributes' => $data['attributes'],
            ];
            if ($hasStatusOnly) {
                $payload['status_only'] = $data['status_only'];
            }
            ComponentType::updateOrCreate(
                ['slug' => $data['slug']],
                $payload
            );
        }
    }
}
