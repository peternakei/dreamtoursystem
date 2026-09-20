<?php

namespace App\Project\Modules\System\Bookings\ApiControllers;

use App\Project\Modules\System\Bookings\Services\Api\CancelBookingFormAction;
use App\Project\Modules\System\Bookings\Services\Api\GetAllBookingsFormAction;
use App\Project\Modules\System\Bookings\Services\Api\GetBookingDetailsFormAction;
use App\Project\Modules\System\Bookings\Services\Api\SaveBookingFormAction;
use App\Http\Controllers\Controller;
use App\Project\Modules\System\Bookings\Requests\Api\CancelBookingFormRequest;
use App\Project\Modules\System\Bookings\Requests\Api\GetAllBookingsFormRequest;
use App\Project\Modules\System\Bookings\Requests\Api\GetBookingDetailsFormRequest;
use App\Project\Modules\System\Bookings\Requests\Api\SaveBookingFormRequest;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function saveBooking(SaveBookingFormRequest $request, SaveBookingFormAction $saveBookingFormAction)
    {
        return $saveBookingFormAction->handle($request);
    }

    public function getBookings(GetAllBookingsFormRequest $request, GetAllBookingsFormAction $getAllBookingsFormAction)
    {
        return $getAllBookingsFormAction->handle($request);
    }

    public function getBookingDetails(GetBookingDetailsFormRequest $request, GetBookingDetailsFormAction $getBookingDetailsFormAction)
    {
        return $getBookingDetailsFormAction->handle($request);
    }

    public function cancelBooking(CancelBookingFormRequest $request, CancelBookingFormAction $cancelBookingFormAction)
    {
        return $cancelBookingFormAction->handle($request);
    }
}
