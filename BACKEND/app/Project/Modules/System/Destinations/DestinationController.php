<?php

namespace App\Project\Modules\System\Destinations;

use App\Project\Modules\System\Destinations\Services\AssignDestinationActivityFormAction;
use App\Project\Modules\System\Destinations\Services\AssignDestinationCategoryFormAction;
use App\Project\Modules\System\Destinations\Services\ChangeDestinationStatusFormAction;
use App\Project\Modules\System\Destinations\Services\CreateDestinationFactFormAction;
use App\Project\Modules\System\Destinations\Services\SaveNewDestinationFormAction;
use App\Project\Modules\System\Destinations\Services\UpdateDestinationDetailsFormAction;
use App\Project\Modules\System\Destinations\Services\UpdateDestinationFactDetailsFormAction;
use App\Project\Modules\System\Destinations\Services\UploadDestinationImagesFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Destinations\Requests\AssignDestinationActivityFormRequest;
use App\Project\Modules\System\Destinations\Requests\AssignDestinationCategoryFormRequest;
use App\Project\Modules\System\Destinations\Requests\ChangeDestinationStatusFormRequest;
use App\Project\Modules\System\Destinations\Requests\CreateDestinationFactFormRequest;
use App\Project\Modules\System\Destinations\Requests\CreateNewDestinationFormRequest;
use App\Project\Modules\System\Destinations\Requests\EditDestinationDetailsFormRequest;
use App\Project\Modules\System\Destinations\Requests\EditDestinationFactDetailsFormRequest;
use App\Project\Modules\System\Destinations\Requests\UploadDestinationImagesFormRequest;
use App\Project\Modules\System\Activities\Activity;
use App\Project\Modules\System\Attachments\Attachment;
use App\Project\Modules\Core\Locations\Location;
use App\Project\Modules\Core\Regions\Region;
use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Destinations\DestinationFact;
use App\Project\Modules\System\Trips\Category;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get destinations
        $destinations = Destination::orderBy('created_at', 'desc')->get();
        $regions = Region::all();
        $locations = Location::all();
        $activities = Activity::all();
        $categories = Category::all();

        return view('web.system.destination.index', ['title' => 'Destinations', 'sub_title' => 'All Destinations', 'destinations' => $destinations, 'regions' => $regions, 'activities' => $activities, 'locations' => $locations, 'categories' => $categories]);
    }

    public function active()
    {
        //get destinations
        $destinations = Destination::where('is_active', true)->orderBy('created_at', 'desc')->get();
        $regions = Region::all();
        $locations = Location::all();
        $activities = Activity::all();
        $categories = Category::all();

        return view('web.system.destination.index', ['title' => 'Destinations', 'sub_title' => 'Active Destinations', 'destinations' => $destinations, 'regions' => $regions, 'activities' => $activities, 'locations' => $locations, 'categories' => $categories]);
    }

    public function inactive()
    {
        //get destinations
        $destinations = Destination::where('is_active', false)->orderBy('created_at', 'desc')->get();
        $regions = Region::all();
        $locations = Location::all();
        $activities = Activity::all();
        $categories = Category::all();

        return view('web.system.destination.index', ['title' => 'Destinations', 'sub_title' => 'Inactive Destinations', 'destinations' => $destinations, 'regions' => $regions, 'activities' => $activities, 'locations' => $locations, 'categories' => $categories]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateNewDestinationFormRequest $request, SaveNewDestinationFormAction $saveNewDestinationFormAction)
    {
        //save
        $save = $saveNewDestinationFormAction->handle($request);
        if (!$save['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $save['message'],
                'redirect' => 'destinations'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $save['message'],
            'redirect' => 'destinations'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //get destination
        $destination = Destination::where('uuid', $id)->first();
        $activities = Activity::all();
        $categories = Category::all();

        return view('web.system.destination.show', ['destination' => $destination, 'activities' => $activities, 'categories' => $categories]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //get destination
        $destination = Destination::where('uuid', $id)->first();
        $regions = Region::all();
        $locations = Location::all();

        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $destination,
            'regions' => $regions,
            'locations' => $locations,
            'html' => '<input type="hidden" name="destination_id" id="destination_id" value="' . $destination->uuid . '">'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditDestinationDetailsFormRequest $request, UpdateDestinationDetailsFormAction $updateDestinationDetailsFormAction, string $id)
    {
        //update
        $update = $updateDestinationDetailsFormAction->handle($request, $id);
        if (!$update['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $update['message'],
                'redirect' => 'destinations'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $update['message'],
            'redirect' => 'destinations'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $destination = Destination::where('uuid', $id)->first();
        $destination->images()->delete();
        $destination->facts()->delete();
        $destination->activities()->delete();
        $delete = $destination->delete();
        if (!$delete) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to delete destination details',
                'redirect' => 'destinations'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Destination deleted successfully',
            'redirect' => 'destinations'
        ]);
    }

    public function changeStatus(ChangeDestinationStatusFormRequest $request, ChangeDestinationStatusFormAction $changeDestinationStatusFormAction, string $id)
    {
        //change
        $change = $changeDestinationStatusFormAction->handle($request, $id);
        if (!$change['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $change['message'],
                'redirect' => 'destinations/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $change['message'],
            'redirect' => 'destinations/' . $id
        ]);
    }

    public function uploadImages(UploadDestinationImagesFormRequest $request, UploadDestinationImagesFormAction $uploadDestinationImagesFormAction, string $id)
    {
        //upload
        $upload = $uploadDestinationImagesFormAction->handle($request, $id);
        if (!$upload['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $upload['message'],
                'redirect' => 'destinations/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $upload['message'],
            'redirect' => 'destinations/' . $id
        ]);
    }

    public function image(string $id)
    {
        $attachment = Attachment::where('uuid', $id)->first();
        $extension = strtolower((string) pathinfo($attachment->name, PATHINFO_EXTENSION));

        $type = '';

        if (in_array($extension, ['jpg', 'png', 'jpeg', 'webp', 'gif', 'bmp'], true)) {
            $type = 'Image';
        } elseif (in_array($extension, ['pdf'])) {
            $type = 'PDF';
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Image fetched successfully',
            'data' => [
                'image' => $attachment,
                'extension' => $type
            ]
        ]);
    }

    public function deleteImage(string $id)
    {
        $attachment = Attachment::where('uuid', $id)->first();

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Image fetched successfully',
            'data' => [
                'image' => $attachment,
            ]
        ]);
    }

    public function destroyImage(string $destination, string $id)
    {
        $delete = Attachment::where('uuid', $id)->delete();
        if (!$delete) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to delete destination image',
                'redirect' => 'destinations/' . $destination
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Destination image deleted successfully',
            'redirect' => 'destinations/' . $destination
        ]);
    }

    public function assignActivity(AssignDestinationActivityFormRequest $request, AssignDestinationActivityFormAction $assignDestinationActivityFormAction, string $id)
    {
        //assign
        $assign = $assignDestinationActivityFormAction->handle($request, $id);
        if (!$assign['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $assign['message'],
                'redirect' => 'destinations/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $assign['message'],
            'redirect' => 'destinations/' . $id
        ]);
    }

    public function createFact(CreateDestinationFactFormRequest $request, CreateDestinationFactFormAction $createDestinationFactFormAction, string $id)
    {
        //create
        $create = $createDestinationFactFormAction->handle($request, $id);
        if (!$create['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $create['message'],
                'redirect' => 'destinations/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $create['message'],
            'redirect' => 'destinations/' . $id
        ]);
    }

    public function fact(string $id)
    {
        $fact = DestinationFact::where('uuid', $id)->first();

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Fact fetched successfully',
            'data' => [
                'fact' => $fact,
            ]
        ]);
    }

    public function updateFact(EditDestinationFactDetailsFormRequest $request, UpdateDestinationFactDetailsFormAction $updateDestinationFactDetailsFormAction, string $destination, string $id)
    {
        //update
        $update = $updateDestinationFactDetailsFormAction->handle($request, $id);
        if (!$update['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $update['message'],
                'redirect' => 'destinations/' . $destination
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $update['message'],
            'redirect' => 'destinations/' . $destination
        ]);
    }

    public function assignCategory(AssignDestinationCategoryFormRequest $request, AssignDestinationCategoryFormAction $assignDestinationCategoryFormAction, string $id)
    {
        //assign
        $assign = $assignDestinationCategoryFormAction->handle($request, $id);
        if (!$assign['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $assign['message'],
                'redirect' => 'destinations/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $assign['message'],
            'redirect' => 'destinations/' . $id
        ]);
    }
}
