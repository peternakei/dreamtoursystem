<?php

namespace App\Project\Modules\System\Library;

use App\Http\Controllers\Controller;
use App\Project\Modules\System\Accommodations\Accommodation;
use App\Project\Modules\System\Activities\Activity;
use App\Project\Modules\Core\Countries\Country;
use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Trips\Category;
use App\Project\Modules\System\Vehicles\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LibraryDashboardController extends Controller
{
    public function index()
    {
        return view('web.system.library.index', [
            'title' => 'Content Library',
            'sub_title' => 'All Library Entities',
            'entities' => $this->entityCards(),
        ]);
    }

    protected function entityCards(): array
    {
        return [
            $this->card(
                slug: 'destinations',
                label: 'Destinations',
                icon: 'mdi mdi-map-marker-radius-outline',
                color: '#0ea5e9',
                modelClass: Destination::class,
                indexRoute: 'destinations.index',
                description: 'Parks, regions and places guests visit. Auto-fills day cards with images and descriptions.'
            ),
            $this->card(
                slug: 'accommodations',
                label: 'Accommodations',
                icon: 'mdi mdi-home-variant-outline',
                color: '#10b981',
                modelClass: Accommodation::class,
                indexRoute: 'accommodations.index',
                description: 'Lodges, camps, hotels and villas. Linked to destinations; library content auto-fills the day cards.'
            ),
            $this->card(
                slug: 'activities',
                label: 'Activities',
                icon: 'mdi mdi-binoculars',
                color: '#f59e0b',
                modelClass: Activity::class,
                indexRoute: 'activities.index',
                description: 'Game drives, walks, cultural visits. Add images so each activity stands out on the proposal.'
            ),
            $this->card(
                slug: 'categories',
                label: 'Categories',
                icon: 'mdi mdi-tag-multiple',
                color: '#ec4899',
                modelClass: Category::class,
                indexRoute: 'categories.index',
                description: 'Categorize trips and destinations: First-Time, Honeymoon, Luxury, etc. Cover images feed the marketing site.'
            ),
            $this->card(
                slug: 'vehicles',
                label: 'Vehicles',
                icon: 'mdi mdi-car-outline',
                color: '#6366f1',
                modelClass: Vehicle::class,
                indexRoute: 'vehicles.index',
                description: 'Your fleet. Surfaced on page 3 of every proposal PDF.'
            ),
            $this->card(
                slug: 'countries',
                label: 'Countries',
                icon: 'mdi mdi-flag-outline',
                color: '#84cc16',
                modelClass: Country::class,
                indexRoute: null,
                description: 'Country reference list. Read-only for now.'
            ),
        ];
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    protected function card(
        string $slug,
        string $label,
        string $icon,
        string $color,
        string $modelClass,
        ?string $indexRoute,
        string $description,
    ): array {
        $query = $modelClass::query();

        $total = (clone $query)->count();
        $active = $this->countActive($modelClass, $query);

        return [
            'slug' => $slug,
            'label' => $label,
            'icon' => $icon,
            'color' => $color,
            'description' => $description,
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'index_route' => $indexRoute,
        ];
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    protected function countActive(string $modelClass, Builder $base): int
    {
        $instance = new $modelClass();
        if (in_array('is_active', $instance->getFillable(), true) || $instance->getConnection()->getSchemaBuilder()->hasColumn($instance->getTable(), 'is_active')) {
            return (clone $base)->where('is_active', true)->count();
        }
        return (clone $base)->count();
    }
}
