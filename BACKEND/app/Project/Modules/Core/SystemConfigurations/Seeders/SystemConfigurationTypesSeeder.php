<?php

namespace App\Project\Modules\Core\SystemConfigurations\Seeders;

use App\Project\Modules\Core\SystemConfigurations\SystemConfigurationType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemConfigurationTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //types
        $types = [
            'Auction Time'
        ];

        foreach ($types as $type) {
            SystemConfigurationType::create([
                'name' => $type,
            ]);
        }
    }
}
