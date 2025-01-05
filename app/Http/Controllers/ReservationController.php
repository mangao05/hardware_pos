<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseFormatter;
use App\Http\Requests\ReservationRequest;
use App\Models\Room;

class ReservationController extends Controller
{
    use ResponseFormatter;
    public function store(ReservationRequest $request)
    {
        try {
            $roomIds = $request->room;
            $checkInDate = $request->check_in_date;
            $checkOutDate = $request->check_out_date;

            foreach ($roomIds as $roomId) {
                $isRoomAvailable = !Reservation::whereHas('rooms', function ($query) use ($roomId, $checkInDate, $checkOutDate) {
                    $query->where('room_id', $roomId)
                        ->where(function ($query) use ($checkInDate, $checkOutDate) {
                            $query->whereBetween('check_in_date', [$checkInDate, date('Y-m-d', strtotime($checkOutDate . '-1 day'))])
                                ->orWhereBetween('check_out_date', [date('Y-m-d', strtotime($checkInDate . '+1 day')), $checkOutDate])
                                ->orWhere(function ($query) use ($checkInDate, $checkOutDate) {
                                    $query->where('check_in_date', '<', $checkInDate)
                                        ->where('check_out_date', '>', $checkOutDate);
                                });
                        });
                })->exists();

                if (!$isRoomAvailable) {
                    return $this->error([], "Room with ID {$roomId} is not available for the specified dates.", 422);
                }
            }

            $data = $request->validated();
            $rooms = Room::whereIn('id', $roomIds)->get();
            $data['room_details'] = $rooms;
            unset($data['room']);
            $reservation = Reservation::create($data);
            $reservation->rooms()->attach($roomIds);

            return $this->success($reservation, 'Reservation created successfully!', 201);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }


    public function index(Request $request)
    {
        try {

            $query = Reservation::query();

            if ($request->has('month') && $request->has('year')) {
                $startOfMonth = Carbon::create($request->year, $request->month, 1)->startOfMonth();
                $endOfMonth = Carbon::create($request->year, $request->month, 1)->endOfMonth();

                $query->where(function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->where('check_in_date', '<=', $endOfMonth)
                        ->where('check_out_date', '>=', $startOfMonth);
                });
            }

            $reservations = $query->orderBy('check_in_date')->get();
            return $this->success($reservations);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            return $this->success($reservation);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    public function update(ReservationRequest $request, Reservation $reservation)
    {
        try {
            $roomIds = $request->room;
            $checkInDate = $request->check_in_date;
            $checkOutDate = $request->check_out_date;

            foreach ($roomIds as $roomId) {
                $isRoomAvailable = !Reservation::whereHas('rooms', function ($query) use ($roomId, $checkInDate, $checkOutDate, $reservation) {
                    $query->where('room_id', $roomId)
                        ->where('reservation_id', '!=', $reservation->id) // Exclude the current reservation
                        ->where(function ($query) use ($checkInDate, $checkOutDate) {
                            $query->whereBetween('check_in_date', [$checkInDate, date('Y-m-d', strtotime($checkOutDate . '-1 day'))])
                                ->orWhereBetween('check_out_date', [date('Y-m-d', strtotime($checkInDate . '+1 day')), $checkOutDate])
                                ->orWhere(function ($query) use ($checkInDate, $checkOutDate) {
                                    $query->where('check_in_date', '<', $checkInDate)
                                        ->where('check_out_date', '>', $checkOutDate);
                                });
                        });
                })->exists();

                if (!$isRoomAvailable) {
                    return $this->error([], "Room with ID {$roomId} is not available for the specified dates.", 422);
                }
            }

            $data = $request->validated();
            $rooms = Room::whereIn('id', $roomIds)->get();
            $data['room_details'] = $rooms;
            unset($data['room']);

            $reservation->update($data);
            $reservation->rooms()->sync($roomIds); 

            return $this->success($reservation, 'Reservation updated successfully!', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            $reservation->delete();
            return $this->success($reservation, 'Reservation deleted successfully!');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }
}
