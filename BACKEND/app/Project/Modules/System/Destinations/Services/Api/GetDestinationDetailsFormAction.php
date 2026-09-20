<?php

namespace App\Project\Modules\System\Destinations\Services\Api;

use App\Project\Modules\System\Destinations\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GetDestinationDetailsFormAction
{
    public function handle(Request $request, $id)
    {
        $destination = Destination::where('uuid', $id)->with([
            'location' => function ($query) {
                $query->select('id', 'name');
            },
            'region' => function ($query) {
                $query->select('id', 'name');
            }
        ])->firstOrFail();
        $image = $destination->images()->where('is_active', true)->latest()->first();

        $data = [
            'id' => $destination->id,
            'uuid' => $destination->uuid,
            'name' => $destination->name,
            'description' => $destination->description,
            'latitude' => $destination->latitude,
            'longitude' => $destination->longitude,
            'location' => $destination->location,
            'region' => $destination->region,
            'product_family' => $this->resolveProductFamily($destination->location?->name),
            'is_active' => ($destination->is_active) ? True : False,
            'created_at' => $destination->created_at,
            'banner' => $image?->url ?? '',
        ];

        $category = [];

        foreach ($destination->categories as $cat) {
            $category[] = [
                'id' => $cat->category->id,
                'name' => $cat->category->name,
            ];
        }

        $data['categories'] = $category;
        $data['facts'] = $destination->facts->select('id', 'fact', 'sub_fact', 'description', 'created_at');

        $activity = [];

        foreach ($destination->activities as $act) {
            $activity[] = [
                'id' => $act->activity->id,
                'name' => $act->activity->name,
                'description' => $act->activity->description,
                'created_at' => $act->created_at
            ];
        }

        $data['activities'] = $activity;

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Destination retrieved successfully',
            'data' => [
                'destinations' => $data
            ]
        ]);
    }

    protected function resolveProductFamily(?string $locationName): ?string
    {
        if (!$locationName) {
            return null;
        }

        return Str::contains(Str::lower($locationName), 'zanzibar')
            ? 'zanzibar_tour'
            : 'safari';
    }
}
