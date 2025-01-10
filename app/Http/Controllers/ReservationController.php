<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseFormatter;
use App\Http\Requests\ReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Http\Resources\ReservationDetailsResponse;
use App\Models\ReservationRoomDetails;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    use ResponseFormatter;

    public function index(Request $request)
    {
        try {

            $query = ReservationRoomDetails::query();

            if ($request->has('month') && $request->has('year')) {
                $startOfMonth = Carbon::create($request->year, $request->month, 1)->startOfMonth();
                $endOfMonth = Carbon::create($request->year, $request->month, 1)->endOfMonth();

                $query->where(function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->where('check_in_date', '<=', $endOfMonth)
                        ->where('check_out_date', '>=', $startOfMonth);
                });
            }

            $reservations = $query->orderBy('check_in_date')->get();

            return $this->success(ReservationDetailsResponse::collection($reservations));
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

    public function store(ReservationRequest $request)
    {
        try {
            DB::beginTransaction();
            $reservation = Reservation::createWithDetails($request->all());
            DB::commit();
            return $this->success($reservation->load('reservationDetails'), 'Reservation created successfully!', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        try {
            DB::beginTransaction();

            $updatedReservation = $reservation->updateWithDetails($reservation, $request->all());

            DB::commit();
            return $this->success($updatedReservation->load('reservationDetails'), 'Reservation updated successfully!', 200);
        } catch (\Exception $e) {
            DB::rollBack();
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

    public function updateReservationRoomStatus(Request $request, $reservation, $room)
    {
        try {
            $roomReservation = ReservationRoomDetails::where('reservation_id', $reservation)
                ->where('room_id', $room)
                ->first();

            $roomReservation->update([
                'status' => $request->status
            ]);

            return $this->success($roomReservation->load(['room', 'reservation']), 'Reservation room status updated successfully!');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    public function changeReservationRoom(Request $request, Reservation $reservation)
    {
        try {
            DB::beginTransaction();
            $unavailableRooms = Reservation::checkRoomAvailability([$request->new_room], $request->check_in_date, $request->check_out_date);
          
            if (!empty($unavailableRooms)) {
                throw new \Exception('The following rooms are not available for the selected dates: ' . implode(', ', $unavailableRooms));
            }

            if ($request->has('old_room')) {
                ReservationRoomDetails::where('reservation_id', $reservation->id)
                    ->where('room_id', $request->old_room)
                    ->delete();
            }

            $reservation->addReservationDetails([$request->new_room], $request->check_in_date, $request->check_out_date);

            DB::commit();
            return $this->success($reservation->load('reservationDetails'), 'Reservation room changed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage());
        }
    }
}
