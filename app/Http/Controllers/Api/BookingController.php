<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Guest;
use App\Models\Booking;
use App\Models\Amenity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate the incoming React wizard data payload structure
        $request->validate([
            'guest.email' => 'required|email',
            'guest.firstName' => 'required|string',
            'guest.lastName' => 'required|string',
            'guest.prefix' => 'nullable|string',
            'guest.birthdate' => 'nullable|date',
            'guest.nationality' => 'nullable|string',
            'guest.countryCode' => 'nullable|string',
            'guest.phone' => 'nullable|string',
            
            'booking.bookingType' => 'required|in:overnight,daytime',
            'booking.startDate' => 'required|date',
            'booking.endDate' => 'required|date',
            'booking.nights' => 'required|integer',
            'booking.basePrice' => 'required|numeric',
            'booking.grandTotal' => 'required|numeric',
            
            'enhancements' => 'required|array' 
        ]);

        try {
            // Use a DB transaction so if anything fails, no partial/corrupted data is saved
            return DB::transaction(function () use ($request) {
                $guestData = $request->input('guest');
                
                // 2. Find existing guest by email or generate a clean new record profile
                $guest = Guest::firstOrCreate(
                    ['email' => $guestData['email']],
                    [
                        'customer_code' => 'CM-CUST-' . strtoupper(Str::random(6)),
                        'prefix' => $guestData['prefix'] ?? null,
                        'first_name' => $guestData['firstName'],
                        'last_name' => $guestData['lastName'],
                        'birthdate' => $guestData['birthdate'] ?? null,
                        'nationality' => $guestData['nationality'] ?? null,
                        'country_code' => $guestData['countryCode'] ?? null,
                        'phone' => $guestData['phone'] ?? null,
                        'guest_type' => 'First-Time'
                    ]
                );

                // If they already existed before this transaction, classify them as Returning
                if (!$guest->wasRecentlyCreated) {
                    $guest->update(['guest_type' => 'Returning']);
                }

                // 3. Create the master reservation entry record
                $bookingData = $request->input('booking');
                $booking = Booking::create([
                    'guest_id' => $guest->id,
                    'booking_type' => $bookingData['bookingType'],
                    'check_in' => $bookingData['startDate'],
                    'check_out' => $bookingData['endDate'],
                    'num_nights' => $bookingData['nights'],
                    'base_price' => $bookingData['basePrice'],
                    'grand_total' => $bookingData['grandTotal'],
                    'status' => 'Confirmed'
                ]);

                // 4. Attach selected enhancements mapping prices directly to your pivot table
                foreach ($request->input('enhancements') as $item) {
                    if (isset($item['active']) && $item['active'] == true) {
                        
                        // FIXED: Using LIKE operator to handle partial matches safely (e.g., 'LPG' matches 'LPG (11 hrs)')
                        $amenity = Amenity::where('name', 'LIKE', '%' . $item['name'] . '%')->first();
                        
                        if ($amenity) {
                            $booking->amenities()->attach($amenity->id, [
                                'quantity' => $item['quantity'] ?? 1,
                                'captured_price' => $item['price']
                            ]);
                        }
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Reservation saved cleanly into database context!',
                    'reference' => 'REF-2026-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT)
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction processing failure.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}