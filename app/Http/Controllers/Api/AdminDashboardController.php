<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Guest;

class AdminDashboardController extends Controller
{
    public function getOverview()
    {
        try {
            // 1. Fetch live Eloquent models from the database (Eager load 'guest' to prevent N+1 issues)
            $bookingModels = Booking::with('guest')->orderBy('created_at', 'desc')->get();
            $guests = Guest::withCount('bookings')->get();

            // 2. Compute metrics calculations directly from the raw database collection values
            $totalBookings = $bookingModels->count();
            
            // Use the actual database column name 'num_nights' here
            $avgStay = $totalBookings > 0 ? round($bookingModels->avg('num_nights'), 1) : 0;
            
            $cancelledCount = $bookingModels->where('status', 'Cancelled')->count();
            $cancellationRate = $totalBookings > 0 ? round(($cancelledCount / $totalBookings) * 100, 1) : 0;
            
            $totalRevenue = $bookingModels->where('status', '!=', 'Cancelled')->sum('grand_total');

            // 3. Now transform/map the collection ithe array structure React expects
            $bookings = $bookingModels->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'guest_name' => $booking->guest ? ($booking->guest->first_name . ' ' . $booking->guest->last_name) : 'Walk-in Guest',
                    'reference' => 'REF-2026-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT),
                    'check_in' => $booking->check_in,
                    'check_out' => $booking->check_out,
                    'nights' => $booking->num_nights, // Mapped to 'nights' for AdminDashboardPage.jsx
                    'type' => $booking->booking_type,
                    'status' => $booking->status,
                    'grand_total' => $booking->grand_total,
                    'email' => $booking->guest?->email ?? ''
                ];
            });

            // 4. Return the clean JSON keys straight to the frontend root context
            return response()->json([
                'success' => true,
                'metrics' => [
                    'totalBookings' => $totalBookings,
                    'alos' => $avgStay . ' nights',
                    'cancellationRate' => $cancellationRate . '%',
                    'revenue' => number_format($totalRevenue, 2, '.', '')
                ],
                'bookings' => $bookings,
                'guests' => $guests
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to build dashboard engine records.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
{
    // Validate the incoming status
    $request->validate([
        'status' => 'required|in:Confirmed,Complete,Cancelled'
    ]);

    try {
        // Find the booking entry
        $booking = Booking::findOrFail($id);
        
        // Update the status condition
        $booking->status = $request->status;
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated successfully to ' . $request->status
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to update booking status.',
            'error' => $e->getMessage()
        ], 500);
    }
}
}