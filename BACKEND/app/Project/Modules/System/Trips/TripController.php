<?php

namespace App\Project\Modules\System\Trips;

use App\Project\Modules\System\Trips\Services\AddTripGroupCampFormAction;
use App\Project\Modules\System\Trips\Services\AddTripGroupFormAction;
use App\Project\Modules\System\Trips\Services\AddTripPointFormAction;
use App\Project\Modules\System\Trips\Services\AddTripPriceFormAction;
use App\Project\Modules\System\Trips\Services\AssignTripAddonFormAction;
use App\Project\Modules\System\Trips\Services\AssignTripCategoryActivityFormAction;
use App\Project\Modules\System\Trips\Services\AssignTripCategoryFormAction;
use App\Project\Modules\System\Trips\Services\AssignTripDestinationFormAction;
use App\Project\Modules\System\Trips\Services\PublishTripFormAction;
use App\Project\Modules\System\Trips\Services\SaveTripBudgetMatrixFormAction;
use App\Project\Modules\System\Trips\Services\SaveNewTripFormAction;
use App\Project\Modules\System\Trips\Services\UpdateTripDetailsFormAction;
use App\Project\Modules\System\Trips\Services\UploadTripBannerFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Trips\Requests\AddTripGroupCampFormRequest;
use App\Project\Modules\System\Trips\Requests\AddTripGroupFormRequest;
use App\Project\Modules\System\Trips\Requests\AddTripPointFormRequest;
use App\Project\Modules\System\Trips\Requests\AddTripPriceFormRequest;
use App\Project\Modules\System\Trips\Requests\AssignTripAddonFormRequest;
use App\Project\Modules\System\Trips\Requests\AssignTripCategoryActivityFormRequest;
use App\Project\Modules\System\Trips\Requests\AssignTripCategoryFormRequest;
use App\Project\Modules\System\Trips\Requests\AssignTripDestinationFormRequest;
use App\Project\Modules\System\Trips\Requests\CreateNewTripFormRequest;
use App\Project\Modules\System\Trips\Requests\EditTripDetailsFormRequest;
use App\Project\Modules\System\Trips\Requests\PublishTripFormRequest;
use App\Project\Modules\System\Trips\Requests\SaveTripBudgetMatrixFormRequest;
use App\Project\Modules\System\Trips\Requests\UploadTripBannerFormRequest;
use App\Project\Modules\System\Activities\Activity;
use App\Project\Modules\System\Addons\Addon;
use App\Project\Modules\Core\AgeGroups\AgeGroup;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Seasons\Season;
use App\Project\Modules\System\Seasons\ServiceClass;
use App\Project\Modules\System\Trips\Category;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\TripCategory;
use App\Project\Modules\System\Trips\TripGroup;
use App\Project\Modules\System\Trips\TripSource;
use App\Project\Modules\System\Trips\TripType;
use App\Project\Modules\System\Trips\TripPoint;
use App\Project\Modules\System\Trips\TripAddon;
use App\Project\Modules\System\Quotations\Services\QuoteBuilderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get trips
        $trips = Trip::orderBy('created_at', 'desc')->get();
        $tripTypes = TripType::all();
        $tripSources = TripSource::where('id', 1)->get();
        $addons = Addon::all();
        $categories = Category::all();
        $destinations = Destination::all();
        $activities = Activity::all();

        return view('web.system.trip.index', ['title' => 'Trips', 'sub_title' => 'All Trips', 'activities' => $activities, 'destinations' => $destinations, 'categories' => $categories, 'addons' => $addons, 'trips' => $trips, 'tripTypes' => $tripTypes, 'tripSources' => $tripSources]);
    }

    public function approved()
    {
        //get trips
        $trips = Trip::where('trip_status_id', 1)->orderBy('created_at', 'desc')->get();
        $tripTypes = TripType::all();
        $tripSources = TripSource::where('id', 1)->get();
        $addons = Addon::all();
        $categories = Category::all();
        $destinations = Destination::all();
        $activities = Activity::all();

        return view('web.system.trip.index', ['title' => 'Trips', 'sub_title' => 'Approved Trips', 'activities' => $activities, 'destinations' => $destinations, 'categories' => $categories, 'addons' => $addons, 'trips' => $trips, 'tripTypes' => $tripTypes, 'tripSources' => $tripSources]);
    }

    public function pending()
    {
        //get trips
        $trips = Trip::where('trip_status_id', 2)->orderBy('created_at', 'desc')->get();
        $tripTypes = TripType::all();
        $tripSources = TripSource::where('id', 1)->get();
        $addons = Addon::all();
        $categories = Category::all();
        $destinations = Destination::all();
        $activities = Activity::all();

        return view('web.system.trip.index', ['title' => 'Trips', 'sub_title' => 'Pending Trips', 'activities' => $activities, 'destinations' => $destinations, 'categories' => $categories, 'addons' => $addons, 'trips' => $trips, 'tripTypes' => $tripTypes, 'tripSources' => $tripSources]);
    }

    public function cancelled()
    {
        //get trips
        $trips = Trip::where('trip_status_id', 3)->orderBy('created_at', 'desc')->get();
        $tripTypes = TripType::all();
        $tripSources = TripSource::where('id', 1)->get();
        $addons = Addon::all();
        $categories = Category::all();
        $destinations = Destination::all();
        $activities = Activity::all();

        return view('web.system.trip.index', ['title' => 'Trips', 'sub_title' => 'Cancelled Trips', 'activities' => $activities, 'destinations' => $destinations, 'categories' => $categories, 'addons' => $addons, 'trips' => $trips, 'tripTypes' => $tripTypes, 'tripSources' => $tripSources]);
    }

    public function completed()
    {
        //get trips
        $trips = Trip::where('trip_status_id', 4)->orderBy('created_at', 'desc')->get();
        $tripTypes = TripType::all();
        $tripSources = TripSource::where('id', 1)->get();
        $addons = Addon::all();
        $categories = Category::all();
        $destinations = Destination::all();
        $activities = Activity::all();

        return view('web.system.trip.index', ['title' => 'Trips', 'sub_title' => 'Completed Trips', 'activities' => $activities, 'destinations' => $destinations, 'categories' => $categories, 'addons' => $addons, 'trips' => $trips, 'tripTypes' => $tripTypes, 'tripSources' => $tripSources]);
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
    public function store(CreateNewTripFormRequest $request, SaveNewTripFormAction $saveNewTripFormAction)
    {
        //save
        $save = $saveNewTripFormAction->handle($request);
        if (!$save['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $save['message'],
                'redirect' => 'trips'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $save['message'],
            'redirect' => 'trips'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //get trip
        $trip = Trip::where('uuid', $id)->first();
        $addons = Addon::all();
        $categories = Category::all();
        $destinations = Destination::all();
        $activities = Activity::all();
        $tripGroups = TripGroup::all();
        $ageGroups = AgeGroup::all();
        $currencies = Currency::all();
        $seasons = Season::all();
        $classes = ServiceClass::all();

        $banner = $trip->banners()->latest()->first();

        $recentInquiries = Inquiry::with('tourist')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('web.system.trip.show', ['banner' => $banner, 'trip' => $trip, 'classes' => $classes, 'seasons' => $seasons, 'currencies' => $currencies, 'ageGroups' => $ageGroups, 'tripGroups' => $tripGroups, 'addons' => $addons, 'activities' => $activities, 'destinations' => $destinations, 'categories' => $categories, 'recentInquiries' => $recentInquiries]);
    }

    /**
     * Apply this trip as a template to a guest inquiry — creates a new
     * QuotationVersion seeded with the trip's days, terms, and prices.
     */
    public function applyToInquiry(Request $request, QuoteBuilderService $quoteBuilderService, string $id)
    {
        $request->validate([
            'inquiry_uuid' => 'required|string|exists:inquiries,uuid',
        ]);

        $trip = Trip::where('uuid', $id)->firstOrFail();

        if (!$trip->is_published) {
            return redirect()->back()->withErrors([
                'inquiry_uuid' => 'This trip must be published before it can be applied to a quote.',
            ]);
        }

        $inquiry = Inquiry::with(['tourist', 'serviceClass'])
            ->where('uuid', $request->input('inquiry_uuid'))
            ->firstOrFail();

        $version = $quoteBuilderService->createFromInquiry(
            $inquiry,
            [
                'trip_id' => $trip->uuid,
                'title' => $trip->name,
                'currency_id' => $inquiry->serviceClass?->currency_id,
                'service_class_id' => $inquiry->service_class_id,
            ],
            Auth::id()
        );

        return redirect()
            ->route('quotation_versions.edit', $version->uuid)
            ->with('success', 'Quote created from "' . $trip->name . '" template.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //get trip
        $trip = Trip::where('uuid', $id)->first();
        $tripTypes = TripType::all();
        $tripSources = TripSource::where('id', 1)->get();

        return response()->json([
            'code' => 200,
            'status' => true,
            'data' => $trip,
            'tripTypes' => $tripTypes,
            'tripSources' => $tripSources,
            'html' => '<input type="hidden" name="trip_id" id="trip_id" value="' . $trip->uuid . '">'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditTripDetailsFormRequest $request, UpdateTripDetailsFormAction $updateTripDetailsFormAction, string $id)
    {
        //update
        $update = $updateTripDetailsFormAction->handle($request, $id);
        if (!$update['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $update['message'],
                'redirect' => 'trips'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $update['message'],
            'redirect' => 'trips'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //get trip
        $delete = Trip::where('uuid', $id)->delete();
        if (!$delete) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to delete trip details',
                'redirect' => 'trips'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Trip deleted successfully',
            'redirect' => 'trips'
        ]);
    }

    public function createAddon(AssignTripAddonFormRequest $request, AssignTripAddonFormAction $assignTripAddonFormAction, string $id)
    {
        //assign
        $assign = $assignTripAddonFormAction->handle($request, $id);
        if (!$assign['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $assign['message'],
                'redirect' => 'trips/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $assign['message'],
            'redirect' => 'trips/' . $id
        ]);
    }

    public function createCategory(AssignTripCategoryFormRequest $request, AssignTripCategoryFormAction $assignTripCategoryFormAction, string $id)
    {
        //assign
        $assign = $assignTripCategoryFormAction->handle($request, $id);
        if (!$assign['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $assign['message'],
                'redirect' => 'trips/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $assign['message'],
            'redirect' => 'trips/' . $id
        ]);
    }

    public function createDestination(AssignTripDestinationFormRequest $request, AssignTripDestinationFormAction $assignTripDestinationFormAction, string $id)
    {
        //assign
        $assign = $assignTripDestinationFormAction->handle($request, $id);
        if (!$assign['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $assign['message'],
                'redirect' => 'trips/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $assign['message'],
            'redirect' => 'trips/' . $id
        ]);
    }

    public function createCategoryActivity(AssignTripCategoryActivityFormRequest $request, AssignTripCategoryActivityFormAction $assignTripCategoryActivityFormAction, string $id)
    {
        //assign
        $assign = $assignTripCategoryActivityFormAction->handle($request, $id);
        if (!$assign['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $assign['message'],
                'redirect' => 'trips/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $assign['message'],
            'redirect' => 'trips/' . $id
        ]);
    }

    public function createPoint(AddTripPointFormRequest $request, AddTripPointFormAction $addTripPointFormAction, string $id)
    {
        //add
        $add = $addTripPointFormAction->handle($request, $id);
        if (!$add['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $add['message'],
                'redirect' => 'trips/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $add['message'],
            'redirect' => 'trips/' . $id
        ]);
    }

    public function createGroup(AddTripGroupFormRequest $request, AddTripGroupFormAction $addTripGroupFormAction, string $id)
    {
        //add
        $add = $addTripGroupFormAction->handle($request, $id);
        if (!$add['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $add['message'],
                'redirect' => 'trips/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $add['message'],
            'redirect' => 'trips/' . $id
        ]);
    }

    public function createGroupCamp(AddTripGroupCampFormRequest $request, AddTripGroupCampFormAction $addTripGroupCampFormAction, string $id)
    {
        //add
        $add = $addTripGroupCampFormAction->handle($request);
        if (!$add['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $add['message'],
                'redirect' => 'trips/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $add['message'],
            'redirect' => 'trips/' . $id
        ]);
    }

    public function createPrice(AddTripPriceFormRequest $request, AddTripPriceFormAction $addTripPriceFormAction, string $id)
    {
        //add
        $add = $addTripPriceFormAction->handle($request, $id);
        if (!$add['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $add['message'],
                'redirect' => 'trips/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $add['message'],
            'redirect' => 'trips/' . $id
        ]);
    }

    public function createBudgetMatrix(SaveTripBudgetMatrixFormRequest $request, SaveTripBudgetMatrixFormAction $saveTripBudgetMatrixFormAction, string $id)
    {
        $save = $saveTripBudgetMatrixFormAction->handle($request, $id);
        if (!$save['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $save['message'],
                'redirect' => 'trips/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $save['message'],
            'redirect' => 'trips/' . $id
        ]);
    }

    public function publish(PublishTripFormRequest $request, PublishTripFormAction $publishTripFormAction, string $id)
    {
        //publish
        $publish = $publishTripFormAction->handle($request, $id);
        if (!$publish['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $publish['message'],
                'redirect' => 'trips/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $publish['message'],
            'redirect' => 'trips/' . $id
        ]);
    }

    public function uploadBanner(UploadTripBannerFormRequest $request, UploadTripBannerFormAction $uploadTripBannerFormAction, string $id)
    {
        //upload
        $upload = $uploadTripBannerFormAction->handle($request, $id);
        if (!$upload['status']) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => $upload['message'],
                'redirect' => 'trips/' . $id
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => $upload['message'],
            'redirect' => 'trips/' . $id
        ]);

    }

    public function deletePoint(string $id)
    {
        //delete trip point
        $point = TripPoint::where('uuid', $id)->first();
        if (!$point) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => 'Trip point not found',
                'redirect' => 'trips'
            ]);
        }

        $delete = $point->delete();
        if (!$delete) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to delete trip point',
                'redirect' => 'trips'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Trip point deleted successfully',
            'redirect' => 'trips'
        ]);
    }

    public function deleteAddon(string $id)
    {
        //delete trip addon
        $addon = TripAddon::where('uuid', $id)->first();
        if (!$addon) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => 'Trip addon not found',
                'redirect' => 'trips'
            ]);
        }

        $delete = $addon->delete();
        if (!$delete) {
            return response()->json([
                'status' => false,
                'code' => 100,
                'message' => 'Failed to delete trip addon',
                'redirect' => 'trips'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Trip addon deleted successfully',
            'redirect' => 'trips'
        ]);
    }

    public function getPoint(string $id)
    {
        //get trip point details
        $point = TripPoint::where('uuid', $id)->first();
        if (!$point) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => 'Trip point not found',
                'redirect' => 'trips'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => $point,
            'html' => ''
        ]);
    }

    public function getAddon(string $id)
    {
        //get trip addon details
        $addon = TripAddon::with('addon')->where('uuid', $id)->first();
        if (!$addon) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => 'Trip addon not found',
                'redirect' => 'trips'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => $addon,
            'html' => ''
        ]);
    }

    public function editTripPoint(string $id)
    {
        //get trip point details for editing
        $point = TripPoint::where('uuid', $id)->first();
        if (!$point) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => 'Trip point not found',
                'redirect' => 'trips'
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => $point,
            'trip' => $point->trip_id,
            'html' => ''
        ]);
    }

    public function updatePoint(Request $request, string $id, string $trip)
    {
        //update trip point
        $point = TripPoint::where('uuid', $id)->first();
        if (!$point) {
            return response()->json([
                'status' => false,
                'code' => 404,
                'message' => 'Trip point not found',
                'redirect' => 'trips'
            ]);
        }

        $point->title = $request->title;
        $point->description = $request->description;
        $point->updated_by = auth('web')->id();

        if ($point->save()) {
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Trip point updated successfully',
                'redirect' => 'trips/' . $trip
            ]);
        }

        return response()->json([
            'status' => false,
            'code' => 100,
            'message' => 'Failed to update trip point',
            'redirect' => 'trips'
        ]);
    }
}
