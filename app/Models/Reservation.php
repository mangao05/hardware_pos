<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\RoomUnavailableException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function reservationDetails()
    {
        return $this->hasMany(ReservationRoomDetails::class);
    }
    /**
     * Create reservation with Room Details
     *
     * @param array $data
     * @return Reservation
     */
    public static function createWithDetails(array $data): Reservation
    {
        $roomIds = $data['room'];
        $checkInDate = $data['check_in_date'];
        $checkOutDate = $data['check_out_date'];

        $unavailableRooms = self::checkRoomAvailability($roomIds, $checkInDate, $checkOutDate);

        if (!empty($unavailableRooms)) {
            throw new RoomUnavailableException($unavailableRooms, 'The following rooms are unavailable.');
        }

        $reservationData = Arr::except($data, ['check_in_date', 'check_out_date', 'room_id', 'room']);

        $reservation = self::create($reservationData);

        $reservation->addReservationDetails($roomIds, $checkInDate, $checkOutDate);

        return $reservation;
    }
    /**
     * Update reservation with Room Details
     *
     * @param array $data
     * @return Reservation
     **/
    public static function updateWithDetails($reservation, array $data): Reservation
    {
        $roomIds = [$data['room']['room_id']];
        $checkInDate = $data['room']['check_in_date'];
        $checkOutDate = $data['room']['check_out_date'];

        // Reuse the checkRoomAvailability method, passing the current reservation ID for exclusion
        $unavailableRooms = self::checkRoomAvailability($roomIds, $checkInDate, $checkOutDate, $reservation->id ?? null);

        if (!empty($unavailableRooms)) {
            throw new RoomUnavailableException($unavailableRooms, 'The following rooms are unavailable.');
        }

        // Update reservation fields
        $reservation->update($data['reservation']);

        // Reuse the addReservationDetails function to update the room details
        $reservation->updateReservationDetails($data['room']);

        return $reservation;
    }
    /**
     * Check Room Availability
     *
     * @param array $roomIds
     * @param string $checkInDate
     * @param string $checkOutDate
     * @param integer|null $excludeReservationId
     * @return array
     */
    public static function checkRoomAvailability(array $roomIds, string $checkInDate, string $checkOutDate, ?int $excludeReservationId = null): array
    {
        $unavailableRooms = [];
        foreach ($roomIds as $roomId) {
            $query = ReservationRoomDetails::where('room_id', $roomId)
                ->where(function ($query) use ($checkInDate, $checkOutDate) {
                    $query->whereBetween('check_in_date', [$checkInDate, date('Y-m-d', strtotime($checkOutDate . '-1 day'))])
                        ->orWhereBetween('check_out_date', [date('Y-m-d', strtotime($checkInDate . '+1 day')), $checkOutDate])
                        ->orWhere(function ($query) use ($checkInDate, $checkOutDate) {
                            $query->where('check_in_date', '<', $checkInDate)
                                ->where('check_out_date', '>', $checkOutDate);
                        });
                });

            if ($excludeReservationId) {
                $query->where('reservation_id', '!=', $excludeReservationId);
            }

            if ($query->exists()) {
                $room_details = $query->first();
                $unavailableRooms[] = $room_details->room_details;
            }
        }
        return $unavailableRooms;
    }

    /**
     * Add Reservation Details
     *
     * @param array $roomIds
     * @param string $checkInDate
     * @param string $checkOutDate
     * @return void
     */
    public function addReservationDetails(array $roomIds, string $checkInDate, string $checkOutDate): void
    {
        foreach ($roomIds as $roomId) {
            $room = Room::findOrFail($roomId); // Ensures the room exists
            $this->reservationDetails()->create([
                'check_in_date' => $checkInDate,
                'check_out_date' => $checkOutDate,
                'room_id' => $room->id,
                'room_details' => $room,
                'status' => 'booked',
            ]);
        }
    }
    /**
     * Update Reservation Room Details
     *
     * @param string $roomId
     * @param string $checkInDate
     * @param string $checkOutDate
     * @param string $status
     * @return void
     */
    private function updateReservationDetails($room): void
    {
        $existingRoom  = ReservationRoomDetails::where('room_id', $room['room_id'])
            ->where('reservation_id', $this->id)
            ->first();

        if ($existingRoom) {
            $existingRoom->update([
                'check_in_date' => $room['check_in_date'],
                'check_out_date' => $room['check_out_date'],
                'room_id' => $room['room_id'],
                'status' => $room['status']
            ]);
        }
    }
}
