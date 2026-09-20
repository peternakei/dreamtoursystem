<?php

namespace App\Project\Modules\System\FaqCategory\Seeders;

use App\Project\Modules\System\Faqs\FaqCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FaqCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //categories
        $categories = [
            [
                'name' => 'Booking Process',
                'slug' => Str::slug('Booking Process'),
                'description' => 'Booking Process',
                'created_by' => 1
            ],
            [
                'name' => 'Tour Details',
                'slug' => Str::slug('Tour Details'),
                'description' => 'Tour Details',
                'created_by' => 1
            ],
            [
                'name' => ' Payment & Pricing',
                'slug' => Str::slug(' Payment & Pricing'),
                'description' => ' Payment & Pricing',
                'created_by' => 1
            ],
            [
                'name' => 'Cancellation & Refunds',
                'slug' => Str::slug('Cancellation & Refunds'),
                'description' => 'Cancellation & Refunds',
                'created_by' => 1
            ],
            [
                'name' => 'Travel Information',
                'slug' => Str::slug('Travel Information'),
                'description' => 'Travel Information',
                'created_by' => 1
            ],
            [
                'name' => 'Customer Support',
                'slug' => Str::slug('Customer Support'),
                'description' => 'Customer Support',
                'created_by' => 1
            ],
            [
                'name' => 'Technical Issues',
                'slug' => Str::slug('Technical Issues'),
                'description' => 'Technical Issues',
                'created_by' => 1
            ],
        ];

        foreach ($categories as $cat) {
            FaqCategory::create($cat);
        }
    }
}
