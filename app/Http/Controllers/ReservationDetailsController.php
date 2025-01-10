<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\ResponseFormatter;
use App\Models\ReservationRoomDetails;

class ReservationDetailsController extends Controller
{
    use ResponseFormatter;
    
    public function deleteReservationRoomDetails($id)
    {
        try {
            $roomDetails = ReservationRoomDetails::find($id);

            if (!$roomDetails) {
                return $this->error([], 'Room reservation details not found.');
            }

            $roomDetails->delete();
            return $this->success([], 'Room reservation details deleted successfully.');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }
}
