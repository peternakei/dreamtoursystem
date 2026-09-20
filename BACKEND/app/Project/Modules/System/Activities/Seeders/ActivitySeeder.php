<?php

namespace App\Project\Modules\System\Activities\Seeders;

use App\Project\Modules\System\Activities\Activity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //activities
        $activities =
            [
                'Hiking' => 'Explore scenic trails and natural landscapes',
                'Sightseeing' => 'Zanzi historical landmarks, museums, or iconic sites',
                'Kayaking' => 'Paddle through rivers, lakes, or coastal waters',
                'Food Tour' => 'Sample local cuisine at restaurants or street markets',
                'Cultural Workshop' => 'Participate in local crafts, cooking, or dance classes',
                'Wildlife Safari' => 'Observe animals in their natural habitat',
                'Beach Relaxation' => 'Lounge or swim at a scenic beach',
                'City Walking Tour' => 'Discover urban history and hidden gems on foot',
                'Adventure Sports' => 'Try ziplining, rock climbing, or paragliding',
                'Shopping' => 'Explore local markets or boutiques for souvenirs',
            ];

        foreach ($activities as $name => $desc) {
            Activity::create([
                'name' => htmlspecialchars($name),
                'description' => htmlspecialchars($desc),
                'created_by' => 1
            ]);
        }
    }
}
