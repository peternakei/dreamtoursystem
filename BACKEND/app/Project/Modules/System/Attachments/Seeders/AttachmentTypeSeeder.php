<?php

namespace App\Project\Modules\System\Attachments\Seeders;

use App\Project\Modules\System\Attachments\AttachmentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttachmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Passport Size' => '#e63946',
            'Identity Image' => '#fb8500',
            'Trip' => '#003049',
            'Destination' => '#003049',
            'Activity' => '#2a9d8f',
            'Blog' => '#264653',
            'Pages' => '#03045e',
            'Trip Banner' => '#e63946',
        ];

        foreach ($types as $type => $color) {
            AttachmentType::create([
                'name' => $type,
                'color' => $color,
            ]);
        }
    }
}
