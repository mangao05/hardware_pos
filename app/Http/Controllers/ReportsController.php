<?php

namespace App\Http\Controllers;

use App\Http\Traits\ResponseFormatter;
use App\Models\ReservationRoomDetails;
use App\Models\Room;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    use ResponseFormatter;
    public function room_statuses()
    {
        $roomsByCategory = Room::with('category:id,display_name,availability')
            ->orderBy('room_category_id')
            ->get()
            ->groupBy(fn($room) => $room->category->display_name)
            ->map(fn($rooms) => [
                'all_rooms' => $rooms->pluck('id')->toArray(),
                'out_of_service' => $rooms->where('availability', false)->pluck('id')->toArray()
            ]);

        $today = request()->get('date', now()->toDateString());

        $occupiedRoomIds = ReservationRoomDetails::whereDate('check_in_date', '<=', $today)
            ->whereDate('check_out_date', '>=', $today)
            ->where('status', 'checkin')
            ->pluck('room_id')
            ->toArray();

        $statusCounts = [];

        foreach ($roomsByCategory as $categoryName => $roomData) {
            $allRooms = $roomData['all_rooms'];
            $outOfServiceRooms = $roomData['out_of_service'];

            $availableRooms = array_diff($allRooms, $occupiedRoomIds, $outOfServiceRooms);
            $occupiedRooms = array_intersect($allRooms, $occupiedRoomIds);

            $statusCounts[$categoryName] = [
                'total' => count($allRooms),
                'available' => count($availableRooms),
                'occupied' => count($occupiedRooms),
                'out_of_service' => count($outOfServiceRooms),
            ];
        }

        return $this->success($statusCounts);
    }
}
