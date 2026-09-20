<?php

namespace App\Project\Modules\System\Bookings;

use App\Http\Controllers\Controller;
use App\Project\Modules\System\Bookings\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get bookings
        $bookings = Booking::orderBy('created_at', 'desc')->get();

        return view('web.system.booking.index', ['title' => 'Bookings', 'sub_title' => 'All Bookings', 'bookings' => $bookings]);
    }

    public function completed()
    {
        //get bookings
        $bookings = Booking::where('booking_status_id', 1)->orderBy('created_at', 'desc')->get();

        return view('web.system.booking.index', ['title' => 'Bookings', 'sub_title' => 'Completed Bookings', 'bookings' => $bookings]);
    }

    public function reserved()
    {
        //get bookings
        $bookings = Booking::where('booking_status_id', 2)->orderBy('created_at', 'desc')->get();

        return view('web.system.booking.index', ['title' => 'Bookings', 'sub_title' => 'Reserved Bookings', 'bookings' => $bookings]);
    }

    public function confirmed()
    {
        //get bookings
        $bookings = Booking::where('booking_status_id', 3)->orderBy('created_at', 'desc')->get();

        return view('web.system.booking.index', ['title' => 'Bookings', 'sub_title' => 'Confirmed Bookings', 'bookings' => $bookings]);
    }

    public function cancelled()
    {
        //get bookings
        $bookings = Booking::where('booking_status_id', 4)->orderBy('created_at', 'desc')->get();

        return view('web.system.booking.index', ['title' => 'Bookings', 'sub_title' => 'Cancelled Bookings', 'bookings' => $bookings]);
    }

    public function expired()
    {
        //get bookings
        $bookings = Booking::where('booking_status_id', 5)->orderBy('created_at', 'desc')->get();

        return view('web.system.booking.index', ['title' => 'Bookings', 'sub_title' => 'Expired Bookings', 'bookings' => $bookings]);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //get bookings
        $booking = Booking::where('uuid', $id)->first();

        return view('web.system.booking.show', ['booking' => $booking]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
