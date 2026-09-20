<?php

namespace App\Project\Modules\System\Pages\Seeders;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Pages\Page;
use Illuminate\Database\Seeder;

class DreamContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('profile', 'SystemUser')->whereHas('roles', fn ($q) => $q->where('name', 'SuperAdmin'))->firstOrFail();
        $pages = [
            ['home', 'Dream Travel and Tours', 'Plan your next journey with us. Tell us where you would like to go and what you need.', 'Voyagez avec Dream Travel and Tours', 'Panga safari yako na Dream Travel and Tours'],
            ['business-travel', 'Business travel', 'Discuss your business itinerary, meeting plans, transport and accommodation requirements with our team.', 'Voyages professionnels', 'Safari za kikazi'],
            ['tours', 'Tours and safaris', 'Explore our published trips and destinations, or send us your preferred route and travel dates.', 'Circuits et safaris', 'Ziara na safari'],
            ['car-rental', 'Car rental', 'Request vehicles for corporate travel, private journeys, weddings or events. Our team will confirm supply and prepare a quotation.', 'Location de voitures', 'Ukodishaji wa magari'],
            ['air-ticketing', 'Flight assistance', 'Share your flight itinerary, dates and passenger details. Our team will follow up with available options and fare conditions.', 'Assistance pour les billets d’avion', 'Msaada wa tiketi za ndege'],
            ['about', 'About Dream Travel and Tours', 'Speak with our team about your travel plans and the support you need for your journey.', 'À propos de Dream Travel and Tours', 'Kuhusu Dream Travel and Tours'],
        ];
        foreach ($pages as $order => [$name,$title,$description,$fr,$sw]) {
            // Never overwrite existing authored pages. Starter copy stays unpublished.
            Page::firstOrCreate(['name' => $name], ['title' => $title, 'description' => $description, 'created_by' => $admin->id, 'sort_order' => $order, 'is_published' => false,
                'translations' => ['fr' => ['title' => $fr], 'sw' => ['title' => $sw]],
                'sections' => [['key' => 'hero', 'type' => 'hero', 'title' => $title, 'text' => $description], ['key' => 'consultation', 'type' => 'consultation', 'title' => 'Plan with our team', 'text' => 'Share your dates, destination and requirements.']],
                'seo' => ['title' => $title, 'description' => $description],
            ]);
        }
    }
}
