<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Http\Requests\ReservationRequest;
use App\Http\Traits\ResponseFormatter;

class ReservationController extends Controller
{
    use ResponseFormatter;
    public function store(ReservationRequest $request)
    {
        try {
            $roomId = $request->room_id;
            $checkInDate = $request->check_in_date;
            $checkOutDate = $request->check_out_date;
            
            $isRoomAvailable = !Reservation::where('room_id', $roomId)
                ->where(function ($query) use ($checkInDate, $checkOutDate) {
                    $query->whereBetween('check_in_date', [$checkInDate, date('Y-m-d', strtotime($checkOutDate . '-1 day'))])
                        ->orWhereBetween('check_out_date', [date('Y-m-d', strtotime($checkInDate . '+1 day')), $checkOutDate])
                        ->orWhere(function ($query) use ($checkInDate, $checkOutDate) {
                            $query->where('check_in_date', '<', $checkInDate)
                                ->where('check_out_date', '>', $checkOutDate);
                        });
                })
                ->exists();

            if (!$isRoomAvailable) {
                return $this->error([], 'The selected room is not available for the specified dates.', 422);
            }

            $reservation = Reservation::create($request->validated());

            return $this->success($reservation->load('room'), 'Reservation created successfully!', 201);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }


    public function index()
    {
        try {
            $reservations = Reservation::with('room')->orderBy('check_in_date')->get();
            return $this->success($reservations);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $reservation = Reservation::with('room')->findOrFail($id);
            return $this->success($reservation);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    public function update(ReservationRequest $request, Reservation $reservation)
    {
        try {
            $roomId = $request->room_id;
            $checkInDate = $request->check_in_date;
            $checkOutDate = $request->check_out_date;

            $isRoomAvailable = !Reservation::where('room_id', $roomId)
                ->where('id', '!=', $reservation->id) // Exclude the current reservation
                ->where(function ($query) use ($checkInDate, $checkOutDate) {
                    $query->whereBetween('check_in_date', [$checkInDate, date('Y-m-d', strtotime($checkOutDate . '-1 day'))])
                        ->orWhereBetween('check_out_date', [date('Y-m-d', strtotime($checkInDate . '+1 day')), $checkOutDate])
                        ->orWhere(function ($query) use ($checkInDate, $checkOutDate) {
                            $query->where('check_in_date', '<', $checkInDate)
                                ->where('check_out_date', '>', $checkOutDate);
                        });
                })
                ->exists();

            if (!$isRoomAvailable) {
                return $this->error([], 'The selected room is not available for the specified dates.', 422);
            }

            $reservation->update($request->validated());

            return $this->success($reservation->load('room'), 'Reservation updated successfully!', 200);
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
