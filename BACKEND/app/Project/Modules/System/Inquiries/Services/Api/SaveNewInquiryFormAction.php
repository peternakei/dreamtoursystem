<?php

namespace App\Project\Modules\System\Inquiries\Services\Api;

use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Tourists\Tourist;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaveNewInquiryFormAction
{
    public function handle(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = $request->user();
            $tourist = null;

            // Always check if tourist exists by email/phone first (regardless of login status)
            $tourist = $this->findOrCreateTourist($request);

            if (!$tourist) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'code' => 100,
                    'message' => 'Failed to save tourist details'
                ]);
            }

            // Save inquiry
            $inquiry = Inquiry::create([
                'tourist_id' => $tourist->id,
                // 'name' => $request->firstName . ' ' . $request->lastName,
                'description' => htmlspecialchars($request->message),
                'from_date' => $request->startDate,
                'to_date' => $request->endDate,
                'trip_type_id' => $request->tripType,
                'destinations' => $request->destinations,
                'locations' => $request->locations,
                'service_class_id' => $request->serviceClass,
                'guests' => $request->guests,
                'budget' => $request->budget,
                'status' => 'pending',
                'user_id' => $user ? $user->id : null, // Only set if user is logged in
                'created_by' => $user ? $user->id : null, // Only set if user is logged in
            ]);

            if (!$inquiry) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'code' => 100,
                    'message' => 'Failed to save inquiry details'
                ]);
            }

            DB::commit();

            // TODO: Send confirmation email and notifications
            // $this->sendConfirmationEmail($inquiry);
            // $this->notifySalesTeam($inquiry);

            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Inquiry submitted successfully',
                'data' => $this->formatInquiryResponse($inquiry, $tourist)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            Log::error('Inquiry creation failed: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => false,
                'code' => 500,
                'message' => 'An error occurred while saving inquiry: ' . $e->getMessage()
            ]);
        }
    }

    private function findOrCreateTourist(Request $request)
    {
        // Check if tourist exists by phone or email
        $existingTourist = Tourist::where('phone', $request->phone)
            ->orWhere('email', $request->email)
            ->first();

        if ($existingTourist) {
            return $existingTourist;
        }

        // Create new tourist
        return Tourist::create([
            'name' => $request->firstName . ' ' . $request->lastName,
            'phone' => $request->phone,
            'email' => $request->email,
            'country_id' => $request->country,
            'address' => '', // Could be added to the form if needed
        ]);
    }

    private function formatInquiryResponse(Inquiry $inquiry, Tourist $tourist)
    {
        return [
            'id' => $inquiry->id,
            'uuid' => $inquiry->uuid,
            'firstName' => explode(' ', $tourist->name)[0] ?? '',
            'lastName' => explode(' ', $tourist->name)[1] ?? '',
            'email' => $tourist->email,
            'phone' => $tourist->phone,
            'country' => $tourist->country_id,
            'tripType' => $inquiry->trip_type_id,
            'destinations' => $inquiry->destinations,
            'locations' => $inquiry->locations,
            'serviceClass' => $inquiry->service_class_id,
            'guests' => $inquiry->guests,
            'startDate' => $inquiry->from_date instanceof \Carbon\Carbon ? $inquiry->from_date->format('Y-m-d') : $inquiry->from_date,
            'endDate' => $inquiry->to_date instanceof \Carbon\Carbon ? $inquiry->to_date->format('Y-m-d') : $inquiry->to_date,
            'budget' => $inquiry->budget,
            'message' => $inquiry->description,
            'status' => $inquiry->status,
            'createdAt' => $inquiry->created_at instanceof \Carbon\Carbon ? $inquiry->created_at->format('Y-m-d H:i:s') : $inquiry->created_at,
            'updatedAt' => $inquiry->updated_at instanceof \Carbon\Carbon ? $inquiry->updated_at->format('Y-m-d H:i:s') : $inquiry->updated_at,
        ];
    }
}
