<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentMode;

class PaymentModeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modes = [
            ['name' => 'espèces', 'display_name' => 'Espèces'],
            ['name' => 'bankily', 'display_name' => 'Bankily'],
            ['name' => 'masrivi', 'display_name' => 'Masrivi'],
            ['name' => 'sedad', 'display_name' => 'Sedad'],
        ];

        foreach ($modes as $mode) {
            PaymentMode::updateOrCreate(
                ['name' => $mode['name']],
                [
                    'display_name' => $mode['display_name'],
                    'is_active' => true
                ]
            );
        }
    }
}
