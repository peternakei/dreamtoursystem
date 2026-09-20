<?php

namespace App\Project\Modules\System\Destinations\Services\Api;

use App\Project\Modules\System\Destinations\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GetDestinationFormAction
{
    public function handle(Request $request)
    {
        $productFamily = $request->input('product_family');
        $limit = $request->integer('limit');
        $offset = max($request->integer('offset', 0), 0);

        $query = Destination::query()
            ->with([
                'location' => function ($query) {
                    $query->select('id', 'name');
                },
                'region' => function ($query) {
                    $query->select('id', 'name');
                }
            ])
            ->when($productFamily === 'zanzibar_tour', function ($query) {
                $query->whereHas('location', function ($locationQuery) {
                    $locationQuery->whereRaw('LOWER(name) LIKE ?', ['%zanzibar%']);
                });
            })
            ->when($productFamily === 'safari', function ($query) {
                $query->whereHas('location', function ($locationQuery) {
                    $locationQuery->whereRaw('LOWER(name) NOT LIKE ?', ['%zanzibar%']);
                });
            })
            ->orderByDesc('id');

        if ($offset > 0) {
            $query->skip($offset);
        }

        if ($limit !== null && $limit > 0) {
            $query->take($limit);
        }

        $destinations = $query->get();

        $data = [];

        for ($i = 0; $i < count($destinations); $i++) {

            $destination = $destinations[$i];
            $image = $destination->images()->where('is_active', true)->latest()->first();

            $data[$i] = [
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

            $data[$i]['categories'] = $category;
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Destinations retrieved successfully',
            'data' => [
                'destinations' => $data,
                'filters' => [
                    'product_family' => in_array($productFamily, ['safari', 'zanzibar_tour'], true) ? $productFamily : null,
                ],
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
